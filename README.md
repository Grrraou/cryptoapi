# cryptoapi
gestionnaires d'actifs crypto

Démo -> http://vps241328.ovh.net/

:: API GET ::

# recupérer un seul coin
api/coinsGet
<p>args {
  id=[0-9+]
  code=[xmr/btc etc...]
  name=[bitcoin, ethereum etc...]
}</p>

# chercher plusieurs coins
api/coinsSearch
args {
  andor=[and/or] #pour la requête SQL ('and' par défaut)
  in=[3,4,18] #liste d'id
  id=[0-9+]
  id_comp=[more/moreeq/less/lesseq] #comparaison par rapport à l'argument id
  code=[xmr/btc etc...] #comparaison LIKE
  name=[bitcoin, ethereum etc...] #comparaison LIKE
  limit=[0-9+]
  all=all #tout récupérer
}

# recupérer un seul wallet
api/walletsGet
args {
  id=[0-9+]
  name=[poloniex, bittrex etc...]
}

# chercher plusieurs coins
api/walletsSearch
args {
  andor=[and/or] #pour la requête SQL ('and' par défaut)
  in=[3,4,18] #liste d'id
  id=[0-9+]
  id_comp=[more/moreeq/less/lesseq] #comparaison par rapport à l'argument id
  name=[bitcoin, ethereum etc...] #comparaison LIKE
  limit=[0-9+]
}

# recupérer un seul asset
api/assetsGet
args {
  id=[0-9+]
  full=full #charge les objets wallet et coin associés également
}

# Retourne les total des valeurs USD & EUR de tous les assets cumulés
api/getFiat

# Met a jour la base de donnée d'un coin
coinmarketcap/update/[xmr/btc/etc ...]/[eur/usd/all]

# Met a jour tous les coins
coinmarketcap/updateAll
args {
  exept=[xmr,btc ...] #liste des cryto a ignoré
}
