<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('components/connection.php');
include('components/queries.php');

try {
    $pokemon = get_all_pokemon_list($conn);

    if (empty($pokemon)) {
        throw new Exception("Nessun Pokémon trovato");
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
    <title>Pokédex - Lista Pokémon</title>
    <meta name="description" content="Esplora la lista completa dei Pokémon con dettagli su tipi, abilità e statistiche.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/pokemon_list.css">
</head>

<body>
    <?php include('components/menu.php'); ?>

    <main class="content">
        <h1>Pokédex</h1>

        <div class="search-container">
            <input type="text" id="search" placeholder="Cerca Pokémon per nome">
        </div>

        <div class="table-container">
            <table id="pokemonTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th class="hide-mobile">Statistiche Base</th>
                        <th class="hide-mobile">Abilità</th>
                        <th class="hide-mobile">Categoria</th>
                        <th class="hide-mobile">Differenza di Genere</th>
                        <th class="hide-mobile">Mega Evoluzione</th>
                        <th class="hide-mobile">Gigamax</th>
                        <th class="hide-mobile">Forma Alpha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pokemon as $poke): ?>
                        <tr>
                            <td data-label="#">
                                <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>">
                                    <?php echo htmlspecialchars($poke['national_pokedex_number']); ?>
                                </a>
                            </td>
                            <td data-label="Icon" class="pokemonIcon">
                                <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>">
                                    <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($poke['pokemon_icon']); ?>" alt="<?php echo htmlspecialchars($poke['name']); ?>">
                                </a>
                            </td>
                            <td data-label="Nome">
                                <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>">
                                    <?php echo htmlspecialchars($poke['name']); ?>
                                </a>
                            </td>
                            <td data-label="Tipo">
                                <?php
                                $type_icons = explode(',', $poke['type_icons']);
                                foreach ($type_icons as $icon):
                                    echo '<img src="assets/images/type/' . htmlspecialchars($icon) . '" alt="Tipo" class="type-image">';
                                endforeach;
                                ?>
                            </td>
                            <td data-label="Statistiche Base" class="hide-mobile"><?php echo htmlspecialchars($poke['base_stats']); ?></td>
                            <td data-label="Abilità" class="hide-mobile">
                                <?php
                                $abilities = explode(',', $poke['abilities']);
                                echo implode('<br>', array_map('htmlspecialchars', $abilities));
                                ?>
                            </td>
                            <td data-label="Categoria" class="hide-mobile"><?php echo htmlspecialchars($poke['category']); ?></td>
                            <td class="hide-mobile">
                                <img src="assets/images/<?php echo $poke['gender_differences'] ? 'Gender_diff.png' : 'Gender_diff_off.png'; ?>"  class="gender_icon">
                            </td>
                            <td class="hide-mobile">
                                <img src="assets/images/megaEvolutions/<?php echo $poke['has_mega_evolution'] ? 'Tretta_Mega_Evolution_icon.png' : 'Tretta_Mega_Evolution_icon_off.png'; ?>" alt="Mega Evolution Icon" class="mega_icon">
                            </td>
                            <td class="hide-mobile">
                                <img src="assets/images/gigamax/<?php echo $poke['has_gigamax'] ? 'Dynamax_icon.png' : 'Dynamax_icon_off.png'; ?>" alt="Dynamax Icon" class="dyna_icon">
                            </td>
                            <td class="hide-mobile">
                                <img src="assets/images/<?php echo $poke['alpha'] ? 'Alpha_icon.png' : 'Alpha_icon_off.png'; ?>" alt="Alpha Form Icon" class="alpha_icon">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.getElementById('search').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#pokemonTable tbody tr');

            rows.forEach(row => {
                let name = row.cells[2].textContent.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>

</html>
