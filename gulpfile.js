import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import terser from 'gulp-terser'

const sass = gulpSass(dartSass)

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    images: 'src/img/**/*.{jpg,jpeg,png,gif,svg}'
}

export function images( done ) {
    src(paths.images)
        .pipe(dest('./public/build/img')); // Puedes añadir más pipes aquí si necesitas optimizar las imágenes
    done();
}

export function css( done ) {
    src(paths.scss, {sourcemaps: true})
        .pipe( sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError) )
        .pipe( dest('./public/build/css', {sourcemaps: '.'}) );
    done()
}

export function js( done ) {
    src(paths.js)
      .pipe(terser())
      .pipe(dest('./public/build/js'))
    done()
}

export function dev() {
    watch( paths.scss, css );
    watch( paths.js, js );
    watch( paths.images, images );
}

export default series( js, css, images, dev )