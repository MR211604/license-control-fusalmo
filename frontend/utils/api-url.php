<?php

// Determinar la URL de la API (Docker vs desarrollo local)
$local_url = 'http://localhost:8080';

$api_url = getenv('API_URL') ?: $local_url;
