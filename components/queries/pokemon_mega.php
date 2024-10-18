<?php
$sql_check_mega_evolution = "
SELECT 
    EXISTS(SELECT 1 FROM pokemon_mega_evolutions WHERE pokemon_mega_evolutions.pokemon_id = ?) AS has_mega_evolution
";

$sql_mega_evolutions = "
SELECT 
    mega_evolutions.mega_evolution_name, 
    mega_evolutions.img, 
    mega_evolutions.ability_id, 
    mega_evolutions.item, 
    mega_evolutions.base_stats, 
    mega_evolutions.hp, 
    mega_evolutions.attack, 
    mega_evolutions.defence, 
    mega_evolutions.sp_attack, 
    mega_evolutions.sp_defence, 
    mega_evolutions.speed,
    mega_evolutions.gradient_color,
    GROUP_CONCAT(DISTINCT types.type_name SEPARATOR ', ') AS type_names,
    GROUP_CONCAT(DISTINCT types.type_name_img SEPARATOR ', ') AS type_images,
    abilities.ability_name AS mega_ability_name,
    abilities.ability_description AS mega_ability_description
FROM 
    pokemon_mega_evolutions
JOIN 
    mega_evolutions ON pokemon_mega_evolutions.mega_id = mega_evolutions.id
LEFT JOIN 
    mega_evolution_type ON mega_evolution_type.mega_evolution_id = mega_evolutions.id
LEFT JOIN 
    types ON mega_evolution_type.type_id = types.id
LEFT JOIN 
    abilities ON mega_evolutions.ability_id = abilities.id
WHERE 
    pokemon_mega_evolutions.pokemon_id = ?
GROUP BY 
    mega_evolutions.id
";
