<?php
function check_gigamax_forms($conn, $pokemon_id) {
    $sql_check_gigamax_forms = "
    SELECT 
        EXISTS(SELECT 1 FROM pokemon_gigamax WHERE pokemon_gigamax.pokemon_id = ?) AS has_gigamax
    FROM 
        pokemon
    WHERE 
        id = ?";
    $stmt = $conn->prepare($sql_check_gigamax_forms);
    $stmt->bind_param("ii", $pokemon_id, $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['has_gigamax'];
}

function get_gigamax_forms($conn, $pokemon_id) {
    $sql_get_gigamax_forms = "
    SELECT 
        pokemon_gigamax.gigamax_form_name, 
        pokemon_gigamax.img
    FROM 
        pokemon_gigamax
    WHERE 
        pokemon_gigamax.pokemon_id = ?";
    $stmt = $conn->prepare($sql_get_gigamax_forms);
    $stmt->bind_param("i", $pokemon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
