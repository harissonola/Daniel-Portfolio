.PHONY: deploy

# ==============================================================================
# CIBLE DE DÉPLOIEMENT POUR HOSTINGER
# ==============================================================================
# Cette unique commande fait tout : se connecte, met à jour le code,
# et lance la nouvelle séquence de commandes d'installation.
deploy:
	ssh -p 65002 u748728060@82.197.83.146 "cd ~/domains/smileupplatform.com/public_html/danielw && \
	git pull origin main && \
	php ~/composer.phar install --no-dev --optimize-autoloader && \
	php bin/console d:s:u -f --no-interaction && \
	php bin/console importmap:install && \
	php bin/console asset-map:compile && \
	php ~/composer.phar dump-env prod && \
	php ~/composer.phar dump-autoload --no-dev --optimize && \
	php bin/console cache:clear --no-warmup"