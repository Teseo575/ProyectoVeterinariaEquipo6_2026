#!/usr/bin/env bash

while true; do
	clear
	echo "===== MENÚ ====="
	echo "1) Saludar"
	echo "2) Mostrar fecha"
	echo "3) Salir"
	read -rp "Elige una opción: " opcion

	case "$opcion" in
		1)
			echo "¡Hola!"
			;;
		2)
			date
			;;
		3)
			echo "Saliendo..."
			exit 0
			;;
		*)
			echo "Opción inválida."
			;;
	esac

	read -rp "Presiona Enter para continuar..."
done
