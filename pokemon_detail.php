<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('components/connection.php');
include('components/queries.php');

try {
    if (!isset($_GET['id'])) {
        throw new Exception("ID del Pokémon non fornito");
    }

    $id = $_GET['id'];
    $pokemon_data = [];

    $totalPokemons = get_total_pokemon_count($conn);
    $pokemon_data['base'] = get_pokemon_base_info($conn, $id);

    if (!$pokemon_data['base']) {
        throw new Exception("Nessun Pokémon trovato con l'ID fornito");
    }

    // Modifichiamo il controllo per la forma Gigamax
    $has_gigamax = !empty($pokemon_data['base']['gigamax']);
    $gigamax_form = $has_gigamax ? [
        'img' => $pokemon_data['base']['gigamax']
    ] : null;

    $primary_type_gradient = isset($pokemon_data['base']['primary_type_gradient']) ? $pokemon_data['base']['primary_type_gradient']
        : 'linear-gradient(100deg, rgba(203,200,193,1) 0%, rgba(60,67,85,1) 100%)';

    $primary_type_color_light = isset($pokemon_data['base']['primary_type_color_light']) && !empty($pokemon_data['base']['primary_type_color_light'])
        ? $pokemon_data['base']['primary_type_color_light'] : '#CBC8C1';

    $pokemon_data['types'] = get_pokemon_types($conn, $pokemon_data['base']['id']);
    $pokemon_data['egg_groups'] = get_pokemon_egg_groups($conn, $pokemon_data['base']['id']);
    $pokemon_data['abilities'] = get_pokemon_abilities($conn, $pokemon_data['base']['id']);
    $pokemon_data['evolves_from'] = get_pokemon_pre_evolution($conn, $pokemon_data['base']['id']);
    $pokemon_data['evolves_to'] = get_pokemon_next_evolution($conn, $pokemon_data['base']['id']);

    $has_mega = check_mega_evolution($conn, $pokemon_data['base']['id']);
    if ($has_mega) {
        $pokemon_data['mega_evolutions'] = get_mega_evolutions_info($conn, $pokemon_data['base']['id']);
    } else {
        $pokemon_data['mega_evolutions'] = [];
    }

    $pokemon_data['base']['has_mega_evolution'] = !empty($pokemon_data['mega_evolutions']);
    $pokemon_data['base']['has_regional_form'] = false; // Aggiorna in base alla tua logica

} catch (Exception $e) {
    echo "Si è verificato un errore: " . $e->getMessage();
    exit;
}
//COPIARE DOPO
//        <img src="assets/images/gigamax/Dynamax_icon.png" class="dyna_icon">

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

    <div class="container" style="background: <?php echo htmlspecialchars($primary_type_color_light); ?>;">
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
                <div>
                    <div class="poke-img-box" onclick="flipImage(this)">
                        <div class="poke-img-flipper">
                            <div class="poke-img">
                                <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($pokemon_data['base']['img']); ?>" alt="<?php echo htmlspecialchars($pokemon_data['base']['name']); ?>">
                            </div>
                            <?php if ($has_gigamax): ?>
                                <div class="gigamax-form">
                                    <img src="assets/images/gigamax/<?php echo htmlspecialchars($gigamax_form['img']); ?>" alt="Gigamax form">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="poke-type">
                        <p><strong>Type:</strong></p>
                        <?php foreach ($pokemon_data['types'] as $type): ?>
                            <div class="poke-type">
                                <img src="assets/images/type/<?php echo htmlspecialchars($type['type_name_img']); ?>" alt="Type <?php echo htmlspecialchars($type['type_name']); ?>" class="type-image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="info-list">
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

                        <div class="info-p">
                            <p><strong>Levelling Rate:</strong>
                                <span class="levelling-rate-name" data-tooltip="EXP: <?php echo htmlspecialchars($pokemon_data['base']['exp_tot']); ?>">
                                    <?php echo htmlspecialchars($pokemon_data['base']['levelling_rate']); ?>
                                </span>
                            </p>

                            <p><strong>Category:</strong> <?php echo htmlspecialchars($pokemon_data['base']['category']); ?> </p>

                            <p><strong>Egg Group:</strong>
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
                                ?></p><br>
                        </div>

                        <div class="info-p">
                            <p><strong>Weight:</strong> <?php echo htmlspecialchars($pokemon_data['base']['weight']); ?> kg </p>
                            <p><strong>Height:</strong> <?php echo htmlspecialchars($pokemon_data['base']['height']); ?> m </p><br>
                        </div>

                        <div class="info-p">
                            <p><strong>Gender Difference:</strong> <?php echo htmlspecialchars($pokemon_data['base']['gender_differences'] ? 'Yes' : 'No'); ?> </p>
                            <p><strong>Mega Evolution:</strong>
                                <?php echo htmlspecialchars(isset($pokemon_data['base']['has_mega_evolution']) ? ($pokemon_data['base']['has_mega_evolution'] ? 'Yes' : 'No') : 'No'); ?> </p>
                            <p><strong>Regional:</strong>
                                <?php echo htmlspecialchars(isset($pokemon_data['base']['has_regional_form']) ? ($pokemon_data['base']['has_regional_form'] ? 'Yes' : 'No') : 'No'); ?></p>
                            <p><strong>Alpha:</strong> <?php echo htmlspecialchars($pokemon_data['base']['alpha'] ? 'Yes' : 'No'); ?></p>
                        </div>
                    </div>

                </div>





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
                        <img src="assets/images/megaEvolutions/Tretta_Mega_Evolution_icon.png" class="mega_icon">
                        <strong>Mega Evolutions:</strong>
                    </div>
                    <div class="mega-evolutions-container">
                        <?php foreach ($pokemon_data['mega_evolutions'] as $mega): ?>
                            <div class="mega-evolution" style="background: <?php echo !empty($mega['gradient_color']) ? htmlspecialchars($mega['gradient_color']) : 'linear-gradient(151deg, rgba(235,18,157,0.3) 0%, rgba(236,83,23,0.3) 13%, rgba(241,252,48,0.3) 33%, rgba(230,248,48,0.3) 45%, rgba(27,185,47,0.3) 71%, rgba(41,219,230,0.3) 91%), white;'; ?>;">
                                <h3><strong><?php echo htmlspecialchars($mega['mega_evolution_name']); ?></strong></h3>
                                <img src="assets/images/megaEvolutions/<?php echo htmlspecialchars($mega['img']); ?>" alt="<?php echo htmlspecialchars($mega['mega_evolution_name']); ?>" class="mega-img">
                                <div class="mega-types">
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



        </div>
    </div>

    <script>
        function flipImage(element) {
            element.classList.toggle('flipped');
        }
    </script>
</body>

</html>
