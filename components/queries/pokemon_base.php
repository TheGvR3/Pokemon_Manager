<?php
$sql_total = "SELECT COUNT(*) AS total FROM pokemon";

$sql_base = "
SELECT pokemon.*, 
    pokemon_base_stats.hp,
    pokemon_base_stats.attack,
    pokemon_base_stats.defence,
    pokemon_base_stats.sp_attack,
    pokemon_base_stats.sp_defence,
    pokemon_base_stats.speed,
    levelling_rate.exp_tot AS exp_tot,
    primary_type_colors.type_color AS primary_type_color,  
    primary_type_colors.gradient_color AS primary_type_gradient,
    primary_type_colors.type_color_light AS primary_type_color_light
FROM pokemon
JOIN pokemon_base_stats ON pokemon.id = pokemon_base_stats.pokemon_id
JOIN levelling_rate ON pokemon.levelling_rate = levelling_rate.name
LEFT JOIN pokemon_types AS primary_type ON pokemon.id = primary_type.pokemon_id 
    AND primary_type.type_position = 'primary'  
LEFT JOIN types AS primary_type_colors ON primary_type.type_id = primary_type_colors.id  
WHERE pokemon.national_pokedex_number = ?
";

$sql_types = "
SELECT types.type_name, types.type_name_img, pokemon_types.type_position
FROM pokemon_types
JOIN types ON pokemon_types.type_id = types.id
WHERE pokemon_types.pokemon_id = ?
ORDER BY pokemon_types.type_position;
";

$sql_egg_groups = "
SELECT egg_groups.group_name
FROM pokemon_egg_groups
JOIN egg_groups ON pokemon_egg_groups.egg_group_id = egg_groups.id
WHERE pokemon_egg_groups.pokemon_id = ?
";

$sql_abilities = "
SELECT abilities.ability_name, abilities.ability_description, pokemon_abilities.ability_type
FROM pokemon_abilities
JOIN abilities ON pokemon_abilities.ability_id = abilities.id
WHERE pokemon_abilities.pokemon_id = ?
ORDER BY pokemon_abilities.ability_type;
";
