<?php
$sql_random_pokemon = 'SELECT p.*, 
GROUP_CONCAT(DISTINCT t.type_name) AS type_names, 
GROUP_CONCAT(DISTINCT t.type_name_img) AS type_name_imgs,
GROUP_CONCAT(DISTINCT pt.type_position) AS type_positions
FROM pokemon p
JOIN pokemon_types pt ON p.id = pt.pokemon_id
JOIN types t ON pt.type_id = t.id
GROUP BY p.id
ORDER BY RAND()
LIMIT 1;
';
