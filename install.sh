UP=$(pgrep mysql | wc -l);
if [ "$UP" -eq 0 ];
then
    echo "MySQL is down or not installed. Exiting...";
    exit
fi

UP=$(pgrep apache2 | wc -l);
if [ "$UP" -eq 0 ];
then
    echo "Apache is down or not installed. Exiting...";
    exit
fi


echo "[ByteJudge-installation] Installing ByteJudge";
echo "[ByteJudge-installation] Configuring MySQL for ByteJudge";
echo -n "[ByteJudge-installation] Enter a password for MySQL ByteJudge users";
read password;
source setupqueries.sql;
echo -n "[MySQL-Root]";
mysql -u root -p <<eof
$mysql_script
eof
echo -n "[ByteJudge-installation] MySQL configured for ByteJudge";
