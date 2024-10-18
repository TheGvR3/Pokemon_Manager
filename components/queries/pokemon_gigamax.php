<?php
$sql_check_gigamax_forms = "
SELECT 
    id,
    EXISTS(SELECT 1 FROM pokemon_gigamax WHERE pokemon_gigamax.pokemon_id = pokemon.id) AS has_gigamax
FROM 
    pokemon
WHERE 
    id = ?";

$sql_get_gigamax_forms = "
SELECT 
    pokemon_gigamax.gigamax_form_name, 
    pokemon_gigamax.img
FROM 
    pokemon_gigamax
WHERE 
    pokemon_gigamax.pokemon_id = ?";
