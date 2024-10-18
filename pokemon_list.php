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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/pokemon_list.css">
</head>

<body>
    <?php include('components/menu.php'); ?>

    <div class="search-container">
        <input type="text" id="search" placeholder="Search Pokemon by name"> <!-- Campo di ricerca -->
    </div>

    <div class="table-container">
        <table id="pokemonTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Abilities</th>
                    <th>Gender Difference</th>
                    <th>Mega Evolution</th> <!-- Intestazione per megaevoluzioni -->
                    <th>Gigamax</th> <!-- Intestazione per gigamax -->
                    <th>Alpha Form</th>
                    <th>Base Stats</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pokemon as $poke): ?>
                    <tr>
                        <td>
                            <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>" class="name">
                                <?php echo htmlspecialchars($poke['national_pokedex_number']); ?>
                            </a>
                        </td>
                        <td class="pokemonIcon">
                            <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>" class="name">
                                <img src="assets/images/pokemonSprites/<?php echo htmlspecialchars($poke['pokemon_icon']); ?>" alt="<?php echo htmlspecialchars($poke['name']); ?>" class="pokemonIcon">
                            </a>
                        </td>
                        <td>
                            <a href="pokemon_detail.php?id=<?php echo htmlspecialchars($poke['national_pokedex_number']); ?>" class="name">
                                <?php echo htmlspecialchars($poke['name']); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            $type_icons = explode(',', $poke['type_icons']);
                            foreach ($type_icons as $icon):
                                echo '<img src="assets/images/type/' . htmlspecialchars($icon) . '" alt="Tipo" class="type-image">'; // Corretto il percorso delle icone dei tipi
                            endforeach;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($poke['category']); ?></td>
                        <td>
                            <?php
                            $abilities = explode(',', $poke['abilities']); // Divide le abilità in un array
                            $abilitiesHtml = [];

                            foreach ($abilities as $index => $ability) {
                                $abilitiesHtml[] = htmlspecialchars($ability) . ($index < count($abilities) - 1 ? ',' : '');
                            }

                            echo implode('<br>', $abilitiesHtml); // Mostra le abilità separate da un a capo
                            ?>
                        </td>

                        <td><?php echo htmlspecialchars($poke['gender_differences'] ? 'Yes' : 'No'); ?></td>

                        <td><?php echo htmlspecialchars($poke['has_mega_evolution'] ? 'Yes' : 'No'); ?></td> <!-- Mostra se ha megaevoluzione -->
                        <td><?php echo htmlspecialchars($poke['has_gigamax'] ? 'Yes' : 'No'); ?></td> <!-- Mostra se ha gigamax -->
                        <td><?php echo htmlspecialchars($poke['alpha'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo htmlspecialchars($poke['base_stats']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Funzionalità di ricerca Pokémon
        document.getElementById('search').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase(); // Ottiene il valore del campo di ricerca in minuscolo
            let rows = document.querySelectorAll('#pokemonTable tbody tr'); // Seleziona tutte le righe della tabella

            rows.forEach(row => { // Cicla attraverso le righe
                let name = row.cells[2].textContent.toLowerCase(); // Ottiene il nome del Pokémon in minuscolo
                row.style.display = name.includes(filter) ? '' : 'none'; // Mostra o nasconde la riga in base al filtro
            });
        });
    </script>
</body>

</html>
