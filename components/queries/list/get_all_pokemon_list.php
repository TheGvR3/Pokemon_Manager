<?php
function get_all_pokemon_list($conn) {
    $sql_all_pokemon_list = '
    SELECT p.*, 
        GROUP_CONCAT(DISTINCT t.type_icon ORDER BY pt.type_position) as type_icons, 
        GROUP_CONCAT(DISTINCT t.type_name ORDER BY pt.type_position) as type_names,
        GROUP_CONCAT(DISTINCT a.ability_name) as abilities,
        EXISTS(SELECT 1 FROM pokemon_mega_evolutions me WHERE me.pokemon_id = p.id) as has_mega_evolution,
        CASE WHEN p.gigamax IS NOT NULL AND p.gigamax != "" THEN 1 ELSE 0 END as has_gigamax
    FROM pokemon p
    JOIN pokemon_types pt ON p.id = pt.pokemon_id
    JOIN types t ON pt.type_id = t.id
    JOIN pokemon_abilities pa ON p.id = pa.pokemon_id
    JOIN abilities a ON pa.ability_id = a.id
    GROUP BY p.id
    ';
    $result = $conn->query($sql_all_pokemon_list);
    return $result->fetch_all(MYSQLI_ASSOC);
}
