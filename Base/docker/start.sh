#!/bin/bash

echo "🛑 Arrêt des conteneurs..."
docker stop $(docker ps -q)
docker compose down --remove-orphans


echo "🔧 Construction de l'image Docker..."

if [ $? -eq 0 ]; then
  echo "🚀 Démarrage des conteneurs..."
  docker compose up -d --build

  echo "✅ Conteneurs démarrés avec succès !"

  echo "Migration de la base de données"
  docker exec -it Planer bash

  php bin/console doctrine:migrations:migrate --no-interaction
  exit
  echo "✅ Migrations terminée avec succès !"
else
  echo "❌ Erreur lors du build, arrêt du script."
  exit 1
fi
