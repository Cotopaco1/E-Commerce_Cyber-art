
import { src, dest, series, watch} from 'gulp'; //Importo las funciones src, dest y series de gulp.
import gulpSass from 'gulp-sass' //importo la funcion para compilar el sass que funciona con gulp.
import * as dartSass from 'sass' //importo todas las funciones del compilador a la variable dartSass
import terser from 'gulp-terser';
import sourcemaps from 'gulp-sourcemaps';
const sass = gulpSass(dartSass);

//Globs o rutas que voy a utilizar mas adelante para pasar como parametro.
// se crea un objeto para poder acceder a ellas mediante paths.scss o paths.js
const paths = {
  scss : 'src/scss/**/*.scss',
  js : 'src/js/**/*.js'
}

function css(done) {

    return src(paths.scss)
      .pipe(sourcemaps.init())
      .pipe(sass().on('error', sass.logError))
      .pipe(sourcemaps.write('.')) 
      .pipe(dest('public_html/build/css'))
    done()
  }

function js(done){

  return src(paths.js)
    .pipe(sourcemaps.init())
    .pipe(terser())
    .pipe(sourcemaps.write('.'))
    .pipe(dest('public_html/build/js'))
    done()

}
function watched(done){

  watch(paths.scss, css) //Funcion que recibe un glob y una tarea a ejecutar cuando note un cambio en los archivos del glob..
  watch(paths.js, js)
  done()                 //glob es un string que contiene las rutas de ciertos archivos definidos por el usuario
}

export default series( css, js, watched )
