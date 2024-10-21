<?php
function get_pokemon_base_info($conn, $id) {
    $sql_pokemon_base_info = "
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
        primary_type_colors.type_color_light AS primary_type_color_light,
        pokemon.gigamax
    FROM pokemon
    JOIN pokemon_base_stats ON pokemon.id = pokemon_base_stats.pokemon_id
    JOIN levelling_rate ON pokemon.levelling_rate = levelling_rate.name
    LEFT JOIN pokemon_types AS primary_type ON pokemon.id = primary_type.pokemon_id 
        AND primary_type.type_position = 'primary'  
    LEFT JOIN types AS primary_type_colors ON primary_type.type_id = primary_type_colors.id  
    WHERE pokemon.national_pokedex_number = ?
    ";
    $stmt = $conn->prepare($sql_pokemon_base_info);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
