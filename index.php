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
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokeManager - Il tuo portale Pokémon</title>
    <meta name="description" content="Scopri le ultime notizie, eventi e informazioni sui Pokémon. PokeManager è la tua fonte principale per tutto ciò che riguarda il mondo Pokémon.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include('components/menu.php'); ?>

    <main class="content">
        <section class="news">
            <div class="news-text">
                <h1>Ultime Notizie Pokémon</h1>
                <p>Scopri le ultime novità del mondo dei Pokémon!</p>
                <ul>
                    <li>Nuovi Pokémon aggiunti alla lista!</li>
                    <li>Eventi speciali in arrivo questo mese!</li>
                    <li>Scopri i nuovi attacchi e abilità!</li>
                </ul>
            </div>
            <div class="news-preview">
                <img src="assets/images/hq720.jpg" alt="Anteprima notizie Pokémon 1" class="img-preview">
                <img src="assets/images/pokemon-multiplayer-game-leak.jpg" alt="Anteprima notizie Pokémon 2" class="img-preview">
                <img src="assets/images/SV08SurgingSparks.jpeg" alt="Anteprima notizie Pokémon 3" class="img-preview">
            </div>
        </section>

        <div class="container">
            <article class="last-news">
                <img src="assets/images/pokemon-multiplayer-game-leak.jpg" alt="MEGA-Leak in Gamefreak" class="news-image">
                <div class="details">
                    <h2>MEGA-Leak in Gamefreak!</h2>
                    <p>Il mondo Pokémon è in subbuglio dopo che una valanga di informazioni riservate provenienti da Game Freak, 
                       la casa di sviluppo della celebre serie, è stata diffusa online. I leak, condivisi inizialmente via X, hanno rapidamente guadagnato l'attenzione dei fan,
                       scatenando discussioni accese e facendo schizzare diversi hashtag in cima alle tendenze.</p>
                    <a href="#" class="read-more">Leggi di più</a>
                </div>
            </article>

            <aside class="pokemon-del-giorno">
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
            </aside>
        </div>
    </main>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>

</html>
