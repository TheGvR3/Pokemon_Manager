<?php
include('components/connection.php');
include('components/queries.php');

try {
    $id = $_GET['id']; // o da dove recuperi l'id del Pokémon
    $pokemon_data = []; // Array per raccogliere i dati

    // Usa $sql_total
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $row_total = $result_total->fetch_assoc();
    $totalPokemons = (int)$row_total['total'];

    // Usa $sql_base
    $stmt_base = $conn->prepare($sql_base);
    $stmt_base->bind_param("i", $id);
    $stmt_base->execute();
    $result_base = $stmt_base->get_result();
    $pokemon_data['base'] = $result_base->fetch_assoc();

    $primary_type_gradient = isset($pokemon_data['base']['primary_type_gradient']) ? $pokemon_data['base']['primary_type_gradient']
        : 'linear-gradient(100deg, rgba(203,200,193,1) 0%, rgba(60,67,85,1) 100%)';

    $primary_type_color_light = isset($pokemon_data['base']['primary_type_color_light']) && !empty($pokemon_data['base']['primary_type_color_light'])
        ? $pokemon_data['base']['primary_type_color_light'] : '#CBC8C1';

    // Usa $sql_types
    $stmt_types = $conn->prepare($sql_types);
    $stmt_types->bind_param("i", $id);
    $stmt_types->execute();
    $result_types = $stmt_types->get_result();
    $pokemon_data['types'] = $result_types->fetch_all(MYSQLI_ASSOC);

    // Usa $sql_egg_groups
    $stmt_egg_groups = $conn->prepare($sql_egg_groups);
    $stmt_egg_groups->bind_param("i", $id);
    $stmt_egg_groups->execute();
    $result_egg_groups = $stmt_egg_groups->get_result();
    $pokemon_data['egg_groups'] = $result_egg_groups->fetch_all(MYSQLI_ASSOC);

    // Usa $sql_abilities
    $stmt_abilities = $conn->prepare($sql_abilities);
    $stmt_abilities->bind_param("i", $id);
    $stmt_abilities->execute();
    $result_abilities = $stmt_abilities->get_result();
    $pokemon_data['abilities'] = $result_abilities->fetch_all(MYSQLI_ASSOC);

    // Usa $sql_evolves_from
    $stmt_evolves_from = $conn->prepare($sql_evolves_from);
    $stmt_evolves_from->bind_param("i", $id);
    $stmt_evolves_from->execute();
    $result_evolves_from = $stmt_evolves_from->get_result();
    $pokemon_data['evolves_from'] = $result_evolves_from->fetch_assoc();

    // Usa $sql_evolves_to
    $stmt_evolves_to = $conn->prepare($sql_evolves_to);
    $stmt_evolves_to->bind_param("i", $id);
    $stmt_evolves_to->execute();
    $result_evolves_to = $stmt_evolves_to->get_result();
    $pokemon_data['evolves_to'] = $result_evolves_to->fetch_assoc();

    // Verifica se ci sono mega evoluzioni
    $stmt_check_mega = $conn->prepare($sql_check_mega_evolution);
    $stmt_check_mega->bind_param("i", $id);
    $stmt_check_mega->execute();
    $result_check_mega = $stmt_check_mega->get_result();
    $has_mega = $result_check_mega->fetch_assoc()['has_mega_evolution'];

    if ($has_mega) {
        $stmt_mega = $conn->prepare($sql_mega_evolutions);
        $stmt_mega->bind_param("i", $id);
        $stmt_mega->execute();
        $result_mega = $stmt_mega->get_result();
        $pokemon_data['mega_evolutions'] = $result_mega->fetch_all(MYSQLI_ASSOC);
    } else {
        $pokemon_data['mega_evolutions'] = [];
    }

    // Verifica se ci sono forme Gigamax
    $stmt_check_gigamax = $conn->prepare($sql_check_gigamax_forms);
    $stmt_check_gigamax->bind_param("i", $id);
    $stmt_check_gigamax->execute();
    $result_check_gigamax = $stmt_check_gigamax->get_result();
    $has_gigamax = $result_check_gigamax->fetch_assoc()['has_gigamax'];

    if ($has_gigamax) {
        $stmt_gigamax = $conn->prepare($sql_get_gigamax_forms);
        $stmt_gigamax->bind_param("i", $id);
        $stmt_gigamax->execute();
        $result_gigamax = $stmt_gigamax->get_result();
        $pokemon_data['gigamax'] = $result_gigamax->fetch_all(MYSQLI_ASSOC);
    } else {
        $pokemon_data['gigamax'] = [];
    }

    $pokemon_data['base']['has_mega_evolution'] = !empty($pokemon_data['mega_evolutions']);
    $pokemon_data['base']['has_gigamax'] = !empty($pokemon_data['gigamax']);
    $pokemon_data['base']['has_regional_form'] = false; // Aggiorna in base alla tua logica

} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pokemon_data['base']['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/pokemon_details.css">
</head>

<body>
    <?php include('components/menu.php'); ?>

    <div class="container" style="background: <?php echo !empty($primary_type_color_light) ? htmlspecialchars($primary_type_color_light) : 'linear-gradient(to right, #ffcc00, #ff9900)'; ?>;">
        <!-- Navigation for Previous and Next Pokémon -->
        <div class="navigation">
            <?php if ($pokemon_data['base']['national_pokedex_number'] > 1): ?>
                <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($pokemon_data['base']['national_pokedex_number'] - 1); ?>" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Previous
                </a>
            <?php else: ?>
                <span class="nav-link" style="visibility: hidden;">Previous</span>
            <?php endif; ?>

            <?php if ($pokemon_data['base']['national_pokedex_number'] < $totalPokemons): ?>
                <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($pokemon_data['base']['national_pokedex_number'] + 1); ?>" class="nav-link">
                    Next <i class="fas fa-arrow-right"></i>
                </a>
            <?php else: ?>
                <span class="nav-link" style="visibility: hidden;">Next</span>
            <?php endif; ?>
        </div>


        <div class="box">
            <h1 class="namep" style="color: <?php echo htmlspecialchars($pokemon_data['base']['primary_type_color']); ?>;">
                <strong>#<?php echo htmlspecialchars($pokemon_data['base']['national_pokedex_number']); ?></strong>
                <?php echo htmlspecialchars($pokemon_data['base']['name']); ?>
            </h1>
            <p class="gen"><strong>Generation:</strong> <?php echo htmlspecialchars($pokemon_data['base']['gen_of_introduction']); ?></p>
            <div class="img-box">
                <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($pokemon_data['base']['img']); ?>" alt="<?php echo htmlspecialchars($pokemon_data['base']['name']); ?>" class="poke-img">
                <p>
                    <strong>Category:</strong> <?php echo htmlspecialchars($pokemon_data['base']['category']); ?> <br>

                    <strong>Egg Group:</strong>
                    <?php
                    if (!empty($pokemon_data['egg_groups'])) {
                        // Crea un array temporaneo per raccogliere i nomi dei gruppi uova
                        $egg_group_names = [];

                        // Itera attraverso i gruppi uova e aggiungi il nome all'array
                        foreach ($pokemon_data['egg_groups'] as $egg_group) {
                            $egg_group_names[] = htmlspecialchars($egg_group['group_name']);
                        }

                        // Unisci i nomi dei gruppi uova separandoli con una virgola e uno spazio
                        echo implode(', ', $egg_group_names);
                    } else {
                        // Messaggio di fallback se non ci sono gruppi uova
                        echo "N/A";
                    }
                    ?>
                    <br>

                    <strong>Levelling Rate:</strong>
                    <span class="levelling-rate-name" data-tooltip="EXP: <?php echo htmlspecialchars($pokemon_data['base']['exp_tot']); ?>">
                        <?php echo htmlspecialchars($pokemon_data['base']['levelling_rate']); ?>
                    </span><br> <br>

                    <strong>Weight:</strong> <?php echo htmlspecialchars($pokemon_data['base']['weight']); ?> kg <br>
                    <strong>Height:</strong> <?php echo htmlspecialchars($pokemon_data['base']['height']); ?> m <br><br>

                    <strong>Gender Difference:</strong> <?php echo htmlspecialchars($pokemon_data['base']['gender_differences'] ? 'Yes' : 'No'); ?> <br>

                    <strong>Mega Evolution:</strong>
                    <?php echo htmlspecialchars(isset($pokemon_data['base']['has_mega_evolution']) ? ($pokemon_data['base']['has_mega_evolution'] ? 'Yes' : 'No') : 'No'); ?> <br>

                    <strong>Gigantamax:</strong>
                    <?php echo htmlspecialchars(isset($pokemon_data['base']['has_gigamax']) ? ($pokemon_data['base']['has_gigamax'] ? 'Yes' : 'No') : 'No'); ?> <br>

                    <strong>Regional:</strong>
                    <?php echo htmlspecialchars(isset($pokemon_data['base']['has_regional_form']) ? ($pokemon_data['base']['has_regional_form'] ? 'Yes' : 'No') : 'No'); ?><br>

                    <strong>Alpha:</strong> <?php echo htmlspecialchars($pokemon_data['base']['alpha'] ? 'Yes' : 'No'); ?>
                </p>


            </div>

            <p class="description"><?php echo htmlspecialchars($pokemon_data['base']['pokedex_description']); ?></p>

            <div class="evolutions">
                <?php if (!empty($pokemon_data['evolves_from'])): ?>
                    <div class="evolution-info">
                        <strong>Evolves from:</strong>
                        <div class="ev-pokemon-box">
                            <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($pokemon_data['evolves_from']['evolves_from_pokemon_id']); ?>" class="name">
                                <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($pokemon_data['evolves_from']['evolves_from_pokemon_icon']); ?>" class="evolution-icon">
                            </a>
                            <p><?php echo htmlspecialchars($pokemon_data['evolves_from']['evolves_from_pokemon_name']); ?></p>
                        </div>
                        <p>(<?php echo htmlspecialchars($pokemon_data['evolves_from']['evolves_from_description']); ?>)</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pokemon_data['evolves_to'])): ?>
                    <div class="evolution-info">
                        <strong>Evolves to:</strong>
                        <div class="ev-pokemon-box">
                            <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($pokemon_data['evolves_to']['evolves_to_pokemon_id']); ?>" class="name">
                                <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($pokemon_data['evolves_to']['evolves_to_pokemon_icon']); ?>" class="evolution-icon" alt="<?php echo htmlspecialchars($pokemon_data['evolves_to']['evolves_to_pokemon_name']); ?>">
                            </a>
                            <p><?php echo htmlspecialchars($pokemon_data['evolves_to']['evolves_to_pokemon_name']); ?></p>
                        </div>
                        <p>(<?php echo htmlspecialchars($pokemon_data['evolves_to']['evolves_to_description']); ?>)</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="poke-info">
            <div class="poke-type">
                <p><strong>Type:</strong></p>
                <?php foreach ($pokemon_data['types'] as $type): ?>
                    <div class="poke-type">
                        <img src="assets/images/type/<?php echo htmlspecialchars($type['type_name_img']); ?>" alt="Type <?php echo htmlspecialchars($type['type_name']); ?>" class="type-image">
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ability-box">
                <p><strong>Abilities:</strong>
                    <?php
                    if (!empty($pokemon_data['abilities'])) {
                        // Crea un array per raccogliere i nomi e descrizioni delle abilità
                        $ability_details = [];

                        // Itera attraverso le abilità e crea stringhe con nome, tipo e descrizione
                        foreach ($pokemon_data['abilities'] as $ability) {
                            $ability_name = htmlspecialchars($ability['ability_name']);
                            $ability_type = ucfirst(htmlspecialchars($ability['ability_type'])); // Prima lettera maiuscola
                            $ability_description = htmlspecialchars($ability['ability_description']); // Ottieni la descrizione

                            // Unisci il nome dell'abilità e il suo tipo, aggiungi la descrizione in un attributo data
                            $ability_details[] = "<span class='ability' data-description='" . $ability_description . "'>$ability_name ($ability_type)</span>";
                        }

                        // Unisci i dettagli delle abilità con una virgola e uno spazio
                        echo implode(', ', $ability_details);
                    } else {
                        // Messaggio di fallback se non ci sono abilità
                        echo "N/A";
                    }
                    ?>
                    <br>
                </p>
            </div>

            <div class="pokemon-stats">
                <h3 class="stats-title">STATS</h3>
                <div class="stats-container">
                    <div class="stat stat-hp">HP: <br> <?php echo htmlspecialchars($pokemon_data['base']['hp']); ?></div>
                    <div class="stat stat-sp-attack">Sp.Att.: <?php echo htmlspecialchars($pokemon_data['base']['sp_attack']); ?></div>
                    <div class="stat stat-sp-defence">Sp.Def.: <?php echo htmlspecialchars($pokemon_data['base']['sp_defence']); ?></div>
                    <div class="stat stat-attack">Attack: <?php echo htmlspecialchars($pokemon_data['base']['attack']); ?></div>
                    <div class="stat stat-defence">Defence: <?php echo htmlspecialchars($pokemon_data['base']['defence']); ?></div>
                    <div class="stat stat-speed">Speed: <?php echo htmlspecialchars($pokemon_data['base']['speed']); ?></div>
                </div>
            </div>

            <?php if (!empty($pokemon_data['mega_evolutions'])): ?>
                <div class="mega-evolutions">
                    <div class="m_evo">
                        <img src="assets/images/megaEvolutions/Tretta_Mega_Evolution_icon.png" class="dyna_icon">
                        <strong>Mega Evolutions:</strong>
                    </div>
                    <div class="mega-evolutions-container">
                        <?php foreach ($pokemon_data['mega_evolutions'] as $mega): ?>
                            <div class="mega-evolution" style="background: <?php echo !empty($mega['gradient_color']) ? htmlspecialchars($mega['gradient_color']) : 'linear-gradient(151deg, rgba(235,18,157,0.3) 0%, rgba(236,83,23,0.3) 13%, rgba(241,252,48,0.3) 33%, rgba(230,248,48,0.3) 45%, rgba(27,185,47,0.3) 71%, rgba(41,219,230,0.3) 91%), white;'; ?>;">
                                <p><?php echo htmlspecialchars($mega['mega_evolution_name']); ?></p>
                                <img src="assets/images/megaEvolutions/<?php echo htmlspecialchars($mega['img']); ?>" alt="<?php echo htmlspecialchars($mega['mega_evolution_name']); ?>" class="mega-img">
                                <div class="mega-types">
                                    <h4>Type:</h4>
                                    <?php
                                    $type_names = explode(', ', $mega['type_names']);
                                    $type_images = explode(', ', $mega['type_images']);
                                    foreach ($type_names as $index => $type_name):
                                    ?>
                                        <div class="mega-type">
                                            <img src="assets/images/type/<?php echo htmlspecialchars($type_images[$index]); ?>" alt="<?php echo htmlspecialchars($type_name); ?>" class="type-img">
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Mostra l'abilità della mega evoluzione -->
                                <?php if (!empty($mega['mega_ability_name'])): ?>
                                    <p><b>Ability:</b>
                                        <span class="ability" data-description="<?php echo htmlspecialchars($mega['mega_ability_description']); ?>">
                                            <?php echo htmlspecialchars($mega['mega_ability_name']); ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <p><b>Ability:</b> N/A</p>
                                <?php endif; ?>

                                <p><b>Item:</b> <?php echo !empty($mega['item']) ? htmlspecialchars($mega['item']) : 'N/A'; ?></p>

                                <div class="mega-pokemon-stats">
                                    <h3 class="mega-stats-title">STATS</h3>
                                    <p><strong>BASE STATS:</strong> <?php echo htmlspecialchars($mega['base_stats']); ?></p>
                                    <div class="mega-stats-container">
                                        <div class="mega-stat mega-stat-hp">HP: <br> <?php echo htmlspecialchars($mega['hp']); ?></div>
                                        <div class="mega-stat mega-stat-sp-attack">Sp.Att.: <?php echo htmlspecialchars($mega['sp_attack']); ?></div>
                                        <div class="mega-stat mega-stat-sp-defence">Sp.Def.: <?php echo htmlspecialchars($mega['sp_defence']); ?></div>
                                        <div class="mega-stat mega-stat-attack">Attack: <?php echo htmlspecialchars($mega['attack']); ?></div>
                                        <div class="mega-stat mega-stat-defence">Defence: <?php echo htmlspecialchars($mega['defence']); ?></div>
                                        <div class="mega-stat mega-stat-speed">Speed: <?php echo htmlspecialchars($mega['speed']); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($pokemon_data['gigamax'])): ?>
                <div class="gigamax-evolutions">
                    <div class="dynamax">
                        <img src="assets/images/gigamax/Dynamax_icon.png" class="dyna_icon">
                        <strong>Gigamax Forms:</strong>
                    </div>
                    <div class="gigamax-evolutions-container">
                        <?php foreach ($pokemon_data['gigamax'] as $gigamax): ?>
                            <div class="gigamax-evolution">
                                <img src="assets/images/gigamax/<?php echo htmlspecialchars($gigamax['img']); ?>" alt="<?php echo htmlspecialchars($gigamax['gigamax_form_name']); ?>" class="gigamax-img">
                                <p class="giga-name"><?php echo htmlspecialchars($gigamax['gigamax_form_name']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>