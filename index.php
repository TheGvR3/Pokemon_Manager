<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('components/connection.php');
include('components/queries.php');

try {
    $pokemonDelGiorno = get_random_pokemon($conn);

    if (!$pokemonDelGiorno) {
        throw new Exception("Nessun Pokémon casuale trovato");
    }

} catch (Exception $e) {
    echo "Si è verificato un errore: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokeManager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include('components/menu.php'); ?>

    <div class="content">
        <div class="news">
            <div>
                <h2>Ultime Notizie</h2>
                <p>Scopri le ultime novità del mondo dei Pokémon!</p>
                <ul>
                    <li>Nuovi Pokémon aggiunti alla lista!</li>
                    <li>Eventi speciali in arrivo questo mese!</li>
                    <li>Scopri i nuovi attacchi e abilità!</li>
                </ul>
            </div>
            <div class="news-preview">
                <img src="assets/images/hq720.jpg" class="img-preview">
                <img src="assets/images/pokemon-multiplayer-game-leak.jpg" class="img-preview">
                <img src="assets/images/SV08SurgingSparks.jpeg" class="img-preview">
            </div>
        </div>

        <div class="container">
            <div class="last-news">
                <img src="assets/images/pokemon-multiplayer-game-leak.jpg">
                <div class="details">
                    <h3>MEGA-Leak in Gamefreak!</h3>
                    <p>Il mondo Pokémon è in subbuglio dopo che una valanga di informazioni riservate provenienti da Game Freak, 
                        la casa di sviluppo della celebre serie, è stata diffusa online. I leak, condivisi inizialmente via X, hanno rapidamente guadagnato l'attenzione dei fan,
                        scatenando discussioni accese e facendo schizzare diversi hashtag in cima alle tendenze. Ecco cinque punti salienti di questa fuga di notizie (assolutamente non confermate da Game Freak),
                        che gettano luce su aspetti inediti e a volte inquietanti del mondo Pokémon.
                        I leak hanno portato alla luce racconti popolari di Sinnoh finora inediti, 
                        che dipingono un quadro a tinte fosche del rapporto tra umani e Pokémon. 
                        Una di queste, in particolare, ha turbato i fan, rivelando un lato oscuro e predatorio di Octillery. 
                        Il racconto descrive come questo Pokémon, apparentemente innocuo, adescasse gli umani con la sua luminescenza per poi trascinarli negli abissi marini.
                        Un'immagine ben lontana da quella del Pokémon buffo e giocherellone che conosciamo.
                    </p>
                </div>
            </div>

            <div class="pokemon-del-giorno">
                <h2>Lucky Pokémon!</h2>
                <div class="box">
                    <p><strong>#<?php echo htmlspecialchars($pokemonDelGiorno['national_pokedex_number']); ?></strong> - <?php echo htmlspecialchars($pokemonDelGiorno['name']); ?></p>
                    <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($pokemonDelGiorno['national_pokedex_number']); ?>">
                        <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($pokemonDelGiorno['img']); ?>" alt="<?php echo htmlspecialchars($pokemonDelGiorno['name']); ?>" class="day-pokemon-img">
                    </a>
                    <div class="type_box">
                        <?php
                        $typeNames = explode(',', $pokemonDelGiorno['type_names']);
                        $typeImages = explode(',', $pokemonDelGiorno['type_name_imgs']);
                        $typePositions = explode(',', $pokemonDelGiorno['type_positions']);

                        $types = [];
                        foreach ($typeNames as $index => $name) {
                            $types[] = [
                                'name' => $name,
                                'image' => $typeImages[$index],
                                'position' => $typePositions[$index]
                            ];
                        }

                        usort($types, function ($a, $b) {
                            return ($a['position'] === 'primary' ? 0 : 1) - ($b['position'] === 'primary' ? 0 : 1);
                        });

                        foreach ($types as $type): ?>
                            <div class="poke-type">
                                <img src="assets/images/type/<?php echo htmlspecialchars($type['image']); ?>"
                                    alt="Tipo <?php echo htmlspecialchars($type['name']); ?>"
                                    class="type-image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>

</html>
