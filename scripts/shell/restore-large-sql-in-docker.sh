#!/bin/bash
#Particiona e restaura um arquivo SQL grande em partes menores

# Nome do arquivo SQL completo
SQL_FILE=""

# Nome do banco de dados
DB_NAME=""
DB_USER=""
DB_PASSWORD=""
DOCKER_CONTAINER=""

# Pasta para armazenar as partes do arquivo SQL
TEMP_DIR="temp_sql_parts"
mkdir -p $TEMP_DIR
#
## Divide o arquivo SQL em partes
csplit -z "$SQL_FILE" '/CREATE TABLE/' '{*}' -f "$TEMP_DIR/part_" -b "%02d.sql"

echo "Arquivo dividido em partes na pasta $TEMP_DIR"

# Loop para limpar cada parte do arquivo SQL com o comando sed
#for PART in $TEMP_DIR/*.sql; do
#    echo "Limpando $PART..."
#    sed -i '/\/\*!40101 SET /d' "$PART"
#    if [ $? -ne 0 ]; then
#        echo "Erro ao limpar $PART. Parando o script."
#        exit 1
#    fi
#done

# Loop para executar cada parte no banco de dados
for PART in $TEMP_DIR/*.sql; do
    echo "Restaurando $PART..."
    docker exec -i $DOCKER_CONTAINER mysql -u $DB_USER --password=$DB_PASSWORD $DB_NAME < "$PART"
    if [ $? -ne 0 ]; then
        echo "Erro ao executar $PART. Parando o script."
        exit 1
    fi
done

echo "Todas as partes foram restauradas com sucesso!"
