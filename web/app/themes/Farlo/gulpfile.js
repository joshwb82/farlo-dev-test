const { src, dest, series, parallel, watch } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const postcss = require("gulp-postcss");
const autoprefixer = require("autoprefixer");
const cssnano = require("cssnano");
const sourcemaps = require("gulp-sourcemaps");
const terser = require("gulp-terser");
const rename = require("gulp-rename");

const paths = {
    styles: {
        src: "src/assets/scss/styles.scss",
        watch: "src/assets/scss/**/*.scss",
        dest: "dist/styles"
    },
    scripts: {
        src: "src/assets/scripts/scripts.js",
        watch: "src/assets/scripts/**/*.js",
        dest: "dist/scripts"
    }
};

function styles() {
    return src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on("error", sass.logError))
        .pipe(postcss([autoprefixer()]))
        .pipe(dest(paths.styles.dest))
        .pipe(postcss([cssnano()]))
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("."))
        .pipe(dest(paths.styles.dest));
}

function scripts() {
    return src(paths.scripts.src)
        .pipe(sourcemaps.init())
        .pipe(dest(paths.scripts.dest))
        .pipe(terser())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("."))
        .pipe(dest(paths.scripts.dest));
}

function watcher() {
    watch(paths.styles.watch, styles);
    watch(paths.scripts.watch, scripts);
}

exports.build = series(parallel(styles, scripts));
exports.watch = series(exports.build, watcher);
