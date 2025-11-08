#!/bin/bash

echo "🛑 Arrêt des conteneurs..."
docker compose down

echo "🔧 Construction de l'image Docker..."
docker compose build --no-cache

if [ $? -eq 0 ]; then
  echo "🚀 Démarrage des conteneurs..."
  docker compose up -d
  echo "✅ Conteneurs démarrés avec succès !"
else
  echo "❌ Erreur lors du build, arrêt du script."
  exit 1
fi
