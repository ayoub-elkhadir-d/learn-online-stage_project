/**
 * Entry point loaded only on the learning page — kept out of app.js so the
 * player/progress modules aren't shipped to every other page (code splitting).
 */
import './lesson-progress';
import './player';
