import Phaser from 'phaser';

// Exposé globalement pour que le script de jeu inline (resources/views/game/index.blade.php)
// puisse construire sa Phaser.Scene sans dépendre d'un CDN externe.
window.Phaser = Phaser;
