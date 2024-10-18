<?php
include('components/connection.php');
include('components/queries.php');
// Query per ottenere tutti i dati dalla tabella pokemon con i tipi e le icone
$sql = '
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

// Esegui la query
$result = $conn->query($sql);

// Controlla se ci sono risultati
if ($result === false) {
    die("Errore nella query: " . $conn->error);
}

// Recupera tutti i Pokémon
$pokemon = $result->fetch_all(MYSQLI_ASSOC);

// Chiudi la connessione
$conn->close();
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
                <?php if (!empty($pokemon)): ?>
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
                <?php else: ?>
                    <tr>
                        <td colspan="11">Nessun Pokémon trovato.</td>
                    </tr>
                <?php endif; ?>
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
