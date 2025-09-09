.PHONY: deploy

# ==============================================================================
# CIBLE DE DÉPLOIEMENT POUR HOSTINGER
# ==============================================================================
# Cette unique commande fait tout : se connecte, met à jour le code,
# et lance la nouvelle séquence de commandes d'installation.
deploy:
	ssh -p 65002 u748728060@82.197.83.146 "cd ~/domains/smileupplatform.com/public_html && \
	git pull origin main"