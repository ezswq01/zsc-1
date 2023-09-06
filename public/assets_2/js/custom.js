/* ------------------------------------------------------------------------------
 *
 *  # Custom JS code
 *
 *  Place here all your custom js. Make sure it's loaded after app.js
 *
 * ---------------------------------------------------------------------------- */

const getExportFilename = function (initial) {
    let d = new Date();
    let n = d.getTime();
    return `${initial}_${n}`;
}
