#!/bin/bash
# script de backup do banco de dados do sistema de atividades
# Autor: Gustavo Emmel
mv /var/www/html/atividades/bkp/bkpatividades.txt /var/www/html/atividades/bkp/bkpold.txt
pg_dump atividade -i > /var/www/html/atividades/bkp/bkpatividades.txt
rm -rf /var/www/html/atividades/bkp/bkpold.txt 
