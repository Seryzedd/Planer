#!/bin/bash

echo "🛑 Arrêt des conteneurs..."
docker stop $(docker ps -q)
docker compose down --remove-orphans


echo "🔧 Construction de l'image Docker..."

if [ $? -eq 0 ]; then
  echo "🚀 Démarrage des conteneurs..."
  docker compose up -d --build
  echo "✅ Conteneurs démarrés avec succès !"
else
  echo "❌ Erreur lors du build, arrêt du script."
  exit 1
fi
