<?php
// Query per ottenere il numero totale di Pokémon
$sql_total = "SELECT COUNT(*) AS total FROM pokemon";

// Query base per i dettagli del Pokémon
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

// Query per i tipi del Pokémon
$sql_types = "
SELECT types.type_name, types.type_name_img, pokemon_types.type_position
FROM pokemon_types
JOIN types ON pokemon_types.type_id = types.id
WHERE pokemon_types.pokemon_id = ?
ORDER BY pokemon_types.type_position;
";

// Query per gli egg groups
$sql_egg_groups = "
SELECT egg_groups.group_name
FROM pokemon_egg_groups
JOIN egg_groups ON pokemon_egg_groups.egg_group_id = egg_groups.id
WHERE pokemon_egg_groups.pokemon_id = ?
";

// Query per le abilità del Pokémon
$sql_abilities = "
SELECT abilities.ability_name, abilities.ability_description, pokemon_abilities.ability_type
FROM pokemon_abilities
JOIN abilities ON pokemon_abilities.ability_id = abilities.id
WHERE pokemon_abilities.pokemon_id = ?
ORDER BY pokemon_abilities.ability_type;
";

// Query per l'evoluzione precedente (da)
$sql_evolves_from = "
SELECT evolves_from_pokemon.name AS evolves_from_pokemon_name, 
    evolves_from_pokemon.pokemon_icon AS evolves_from_pokemon_icon,
    pokemon_evolutions.description AS evolves_from_description,
    evolves_from_pokemon.id AS evolves_from_pokemon_id
FROM pokemon_evolutions
JOIN pokemon AS evolves_from_pokemon ON pokemon_evolutions.pokemon_id = evolves_from_pokemon.id
WHERE pokemon_evolutions.evolves_to_pokemon_id = ?;
";

// Query per l'evoluzione successiva (a)
$sql_evolves_to = "
SELECT evolves_to_pokemon.name AS evolves_to_pokemon_name, 
    evolves_to_pokemon.pokemon_icon AS evolves_to_pokemon_icon,
    pokemon_evolutions_evolve_to.description AS evolves_to_description,
    evolves_to_pokemon.id AS evolves_to_pokemon_id
FROM pokemon_evolutions AS pokemon_evolutions_evolve_to
JOIN pokemon AS evolves_to_pokemon ON pokemon_evolutions_evolve_to.evolves_to_pokemon_id = evolves_to_pokemon.id
WHERE pokemon_evolutions_evolve_to.pokemon_id = ?;
";

// Query per ottenere i dati del Pokémon e verificare le mega evoluzioni
$sql_check_mega = "
SELECT 
    pokemon.*, 
    EXISTS(SELECT 1 FROM pokemon_mega_evolutions WHERE pokemon_mega_evolutions.pokemon_id = pokemon.id) AS has_mega_evolution
FROM 
    pokemon 
WHERE 
    pokemon.id = ?";

// Query per le mega evoluzioni
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

// Query per ottenere i dati del Pokémon e verificare le forme Gigamax
$sql_check_gigamax_forms = "
SELECT 
    id,
    EXISTS(SELECT 1 FROM pokemon_gigamax WHERE pokemon_gigamax.pokemon_id = pokemon.id) AS has_gigamax
FROM 
    pokemon
WHERE 
    id = ?";

// Query per ottenere le forme Gigamax di un Pokémon
$sql_get_gigamax_forms = "
SELECT 
    pokemon_gigamax.gigamax_form_name, 
    pokemon_gigamax.img
FROM 
    pokemon_gigamax
WHERE 
    pokemon_gigamax.pokemon_id = ?";

// Query per ottenere tutti i dati dalla tabella pokemon con i tipi e le icone
$sql_all_pokemon = '
SELECT p.*, 
    GROUP_CONCAT(DISTINCT t.type_icon ORDER BY pt.type_position) as type_icons, 
    GROUP_CONCAT(DISTINCT t.type_name ORDER BY pt.type_position) as type_names,
    GROUP_CONCAT(DISTINCT a.ability_name) as abilities,
    EXISTS(SELECT 1 FROM pokemon_mega_evolutions me WHERE me.pokemon_id = p.id) as has_mega_evolution,
    EXISTS(SELECT 1 FROM pokemon_gigamax g WHERE g.pokemon_id = p.id) as has_gigamax
FROM pokemon p
JOIN pokemon_types pt ON p.id = pt.pokemon_id
JOIN types t ON pt.type_id = t.id
JOIN pokemon_abilities pa ON p.id = pa.pokemon_id
JOIN abilities a ON pa.ability_id = a.id
GROUP BY p.id
';

// Query per ottenere un Pokémon casuale + Tipo
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

// Aggiungi queste query alla fine del file

// Query per verificare se un Pokémon ha mega evoluzioni
$sql_check_mega_evolution = "
SELECT 
    EXISTS(SELECT 1 FROM pokemon_mega_evolutions WHERE pokemon_mega_evolutions.pokemon_id = ?) AS has_mega_evolution
";

// Query per le mega evoluzioni (già presente, ma la includiamo per completezza)
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
