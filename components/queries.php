<?php
// Base
require_once __DIR__ . '/queries/base/get_total_pokemon_count.php';
require_once __DIR__ . '/queries/base/get_pokemon_base_info.php';
require_once __DIR__ . '/queries/base/get_pokemon_types.php';
require_once __DIR__ . '/queries/base/get_pokemon_egg_groups.php';
require_once __DIR__ . '/queries/base/get_pokemon_abilities.php';

// Evolution
require_once __DIR__ . '/queries/evolution/get_pokemon_pre_evolution.php';
require_once __DIR__ . '/queries/evolution/get_pokemon_next_evolution.php';

// Forms
require_once __DIR__ . '/queries/forms/get_mega_evolution_info.php';
// require_once __DIR__ . '/queries/forms/get_gigamax_info.php';

// List
require_once __DIR__ . '/queries/list/get_all_pokemon_list.php';
require_once __DIR__ . '/queries/list/get_random_pokemon.php';
