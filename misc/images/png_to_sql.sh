#!/bin/bash

# A script to generate SQL from PNG images
# depends on hexdump

scriptdir="$(dirname $0)"
pngdir="${1:-png_modern}"
sqlbasedir="$scriptdir/../../database"
imagefile="images.sql"

imagefile_mysql="$sqlbasedir/mysql/$imagefile"
imagefile_pgsql="$sqlbasedir/postgresql/$imagefile"
imagefile_sqlite3="$sqlbasedir/sqlite3/$imagefile"
imagefile_oracle="$sqlbasedir/oracle/$imagefile"

oracle_sring_max=2048
oracle_line_max=15

for imagefile in "$imagefile_mysql" "$imagefile_pgsql" "$imagefile_sqlite3" "$imagefile_oracle"; do
	[[ -s "$imagefile" ]] && {
		echo "Non-empty $imagefile already exists, stopping"
		exit 1
	}
done

echo "Generating SQL files"

cat images_oracle_start.txt > "$imagefile_oracle"

imagecount=$(ls $pngdir/*.png | wc -l)

# TODO: this loop won't work with directory names, containing spaces
# using 'find' here seems to be a bit excessive for now
for imagefile in $pngdir/*.png; do
	((imagesdone++))
	imagename="$(basename "${imagefile%.png}")"
	image_data=$(hexdump -ve '"" 1/1 "%02X"' "$imagefile")
	base64 -w$oracle_sring_max "$imagefile" > tmp_b64
	split -l$oracle_line_max tmp_b64 tmp_b64
	rm -rf tmp_b64

	# ----- MySQL
	echo "INSERT INTO 'images' ('imageid','imagetype','name','image') VALUES ($imagesdone,1,'$imagename',0x$image_data);" >> "$imagefile_mysql"
	# ----- PostgreSQL
	echo "INSERT INTO images (imageid,imagetype,name,image) VALUES ($imagesdone,1,'$imagename',decode('$image_data','hex'));" >> "$imagefile_pgsql"
	# ----- Oracle
	echo -e "\tl_clob := EMPTY_CLOB();" >> "$imagefile_oracle"
	for oracle_imagefile in tmp_b64*; do
		image_data_oracle=$(cat "$oracle_imagefile")
		echo -e "\tl_clob := l_clob || '$image_data_oracle';" >> "$imagefile_oracle"
		rm -rf $oracle_imagefile
	done
	echo -e "\tINSERT INTO images VALUES ($imagesdone,1,'$imagename',base64decode(l_clob));"  >> "$imagefile_oracle"
	# ----- SQLite
	echo "INSERT INTO images (imageid,imagetype,name,image) VALUES ($imagesdone,1,'$imagename','$image_data');" >> "$imagefile_sqlite3"

	echo -ne "\b\b\b\b$[$imagesdone*100/$imagecount]% "
	
done
cat images_oracle_end.txt >> "$imagefile_oracle"
echo
