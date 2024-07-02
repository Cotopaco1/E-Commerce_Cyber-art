let montoTotal = 0;
const carritoDeCompra = [
]
const usuario = {
    id : 1,
    nombre : 'sergio silva',
}

const datos = {
    carritoDeCompra : {},
    usuario: {}
}

const path = window.location.pathname;
document.addEventListener('DOMContentLoaded', function(){
iniciarApp();

})

function iniciarApp(){
    eventoInputs(); //Inputs radio
    eventoMenu(); //Menu header
    agregarFixed(); //Menu Header
    subrayarPaginaActual(); //menu Header
    eventoCarritoUserBoton(); //menu Header
    if(path === '/'){
        eventoCuadros();
        consultarApiCuadros();

    }
    if(path === '/carrito_de_compras'){
        crearOpcionesEnSelected();
    }
}

//crear option en el selected
function crearOpcionesEnSelected(){
    const select = document.querySelector('#departamento')
    getDepartamentosColombia()
    .then(datos =>{
        datos.forEach(departamento => {
            const option = document.createElement('OPTION');
            option.textContent = departamento.name
            option.value = departamento.name
            select.appendChild(option);
        });
    })

   
}

//Api departamentos Colombia:
    //crear peticion a api para obtener todos los departamentos de colombia.
let departamentos = {};
async function getDepartamentosColombia(){
    try {
        const url = 'https://api-colombia.com/api/v1/Department';
        const resultado = await fetch(url);
        const json = await resultado.json()
        return json;
    } catch (error) {
        console.log(error);
    }  
}
//Enviar peticion para guardar pedido...
async function enviarDatos (){
    try {
        const fecha = getFechaActualFormateada();
        //Crear objeto que sera convertido a JSON y enviado por fetch POST
        const datos = {
            carritoDeCompra : carritoDeCompra,
            userData : {
                usuarioID : usuario.id
            },
            fecha : fecha,
            status: 'pendiente',
            monto_total : montoTotal
        }
        const url = 'http://localhost:3000/api/cuadros';
        jsonData = JSON.stringify(datos);

        const options = {
            method : 'POST',
            body : jsonData
        }
        //Envio del objeto a la DB
        const respuesta = await fetch(url, options)
        const resultado = await respuesta.json();
        //Alerta si el resultado es exitoso.
        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Pedido creado",
                text: "El pedido ha sido guardado con exito",
                button: 'OK'
              }).then(() =>{
                window.location.reload();       
              })
        }

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Ha habido un error",
            text: "El pedido no se ha podido crear.."
          });
    }
}
function getFechaActualFormateada(){
    const now = new Date();

// Obtener las diferentes partes de la fecha y hora
const year = now.getFullYear();
const month = String(now.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript son 0-11, así que sumamos 1
const day = String(now.getDate()).padStart(2, '0');
const hours = String(now.getHours()).padStart(2, '0');
const minutes = String(now.getMinutes()).padStart(2, '0');
const seconds = String(now.getSeconds()).padStart(2, '0');

// Formatear la fecha y hora en el formato YYYY-MM-DD HH:MM:SS
const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
return formattedDate;
}
//Cuadros //
//Mostrar Resultados ....
//Consultar la api
async function consultarApiCuadros(){
    try {
        const url = 'http://localhost:3000/api/cuadros';
        const resultado = await fetch(url);
        const json = await resultado.json()
        imprimirResultadosApi(json);
    } catch (error) {
        console.log(error);
    }

}
function imprimirResultadosApi(json){

    json.forEach( cuadro =>{
        const { descripcion, disponible, id, imagen, nombre, precio, size} = cuadro
        
        const section = document.getElementById('cuadros');
        
        const cuadroDiv = document.createElement('DIV');
        cuadroDiv.classList.add('cuadro')

        const imagenDiv = document.createElement('DIV')
        imagenDiv.classList.add('imagenCuadroDiv');
        imagenDiv.style.backgroundImage = `url(/img/${imagen})`
        

        const infoDiv = document.createElement('DIV');
        infoDiv.classList.add('cuadro-info');
        infoDiv.dataset.idCuadro = id;

        const nombreCuadro = document.createElement('P')
        nombreCuadro.classList.add('nombre-cuadro');
        nombreCuadro.textContent = nombre;

        const precioCuadro = document.createElement('P')
        precioCuadro.classList.add('precio-cuadro');
        precioCuadro.textContent = precio;

        infoDiv.appendChild(nombreCuadro);
        infoDiv.appendChild(precioCuadro);
        crearBotonComprarActual(infoDiv, cuadro);

        imagenDiv.appendChild(infoDiv);

        cuadroDiv.appendChild(imagenDiv);

        section.appendChild(cuadroDiv);

    })
}
function eventoCuadros(){
    cuadros = document.querySelectorAll('.imagenCuadroDiv');
    cuadros.forEach( cuadro=>{
        cuadro.addEventListener('mouseenter', crearBotonComprar)
        cuadro.addEventListener('mouseleave', eliminarBotonComprar)
    })
    
}

//CrearBotonComprar Actual...
function crearBotonComprarActual(event, cuadro){
    const botonDIV = document.createElement("DIV");
    const boton = document.createElement("BUTTON");
    boton.classList.add('boton');
    boton.textContent = `Agregar al carrito`;

    botonDIV.appendChild(boton);
    event.appendChild(botonDIV);

    boton.addEventListener('click', function(){
        agregarAlCarrito(cuadro);
        
    })
    
}

function agregarAlCarrito(cuadro){
   /*  let cantidad = 0;
    carritoDeCompra = [...carritoDeCompra, cuadro] */
    if('cantidad' in cuadro){
        const {id } = cuadro;
        let value;
        carritoDeCompra.forEach(producto=>{
            if(id === producto.id){
                value = producto;
            }
        })
        const index = carritoDeCompra.indexOf(value)
        carritoDeCompra[index].cantidad = carritoDeCompra[index].cantidad + 1;
    }else{
        cuadro.cantidad = 1;
        carritoDeCompra.push(cuadro)
    }
    
    /* carritoDeCompra.push(cuadro); */
    actualizarInterfazCarritoCompra();
}

// Termina cuadros //

function removeInterfazDinamico(event, interfaz){
    interfaz.remove();
    event.target.removeEventListener('click', removeInterfaz);
}

//Crea un modal en toda la pantalla..., si ya existe solo le quita la clase ocultar y termina la ejecucion...
//Carrito compras
//evento Click carrito de compra, usuario boton HEADER.
function eventoCarritoUserBoton(){
    const carritoCompra = document.querySelector('.carritoCompra');
    const userBoton = document.querySelector('.userBoton');
    carritoCompra.addEventListener('click', mostrarInterfazCarritoCompras);
    userBoton.addEventListener('click', mostrarInterfazCarritoCompras);

    
}
function mostrarInterfazCarritoCompras(){
    actualizarInterfazCarritoCompra();

    const modal = document.querySelector('.modalCarrito');
    const interfazCarritoCompras = document.querySelector('#carritoDeCompras')
    modal.appendChild(interfazCarritoCompras);
    esconderScroll();
}
function actualizarInterfazCarritoCompra(){
    //Si ya existe un modal, entonces solo le quita occultar..
    const modalAnterior = document.querySelector('.modalCarrito');
    if(modalAnterior){
        modalAnterior.remove();
    }
    crearModal();

    const interfaz = document.querySelector('.contenedorCarritoCompras')
    //si el carrito no tiene productos ..
    if(!carritoDeCompra.length >= 1){
        const parrafo = document.createElement('P');
        parrafo.classList.add('parrafoCarritoVacio');
        parrafo.textContent = 'El carrito esta vacio ...'
        interfaz.appendChild(parrafo);
        return
    }
        
        //borras toda la interfaz.
        while(interfaz.firstChild){
            interfaz.removeChild(interfaz.firstChild);
        }
        //Crear productos e insertar en interfaz..
        carritoDeCompra.forEach(producto =>{
            const {id, nombre, precio, descripcion, size, disponible, imagen, cantidad} = producto;
            
            const productoDiv = document.createElement('DIV');
            productoDiv.classList.add("interfazCampo");

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = nombre;
            nombreProducto.classList.add('nombre-producto')
            const precioProducto = document.createElement('P');
            precioProducto.textContent = precio;
            precioProducto.classList.add('precio-producto')
            const cantidadProducto = document.createElement('P');
            cantidadProducto.textContent = `Cantidad: ${cantidad}`;

            const botonMas = document.createElement('BUTTON');
            botonMas.classList.add('botonChico');
            botonMas.textContent = '+';
            botonMas.addEventListener('click', function(){
                agregarCantidad(producto);
            })

            const botonMenos = document.createElement('BUTTON');
            botonMenos.classList.add('botonChicoRojo');
            botonMenos.textContent = '-';
            botonMenos.addEventListener('click', function(){
                restarCantidad(producto);
            })


            

            const divInfo = document.createElement('DIV');
            divInfo.classList.add('infoProductoCarrito');
            divInfo.appendChild(nombreProducto)
            divInfo.appendChild(precioProducto)
            divInfo.appendChild(cantidadProducto)
            divInfo.appendChild(botonMas)
            divInfo.appendChild(botonMenos)
            crearBotonBorrar(divInfo, id, producto );

            const imagenProducto = document.createElement('IMG');
            imagenProducto.src = `/img/${imagen}`;

            productoDiv.appendChild(divInfo);
            productoDiv.appendChild(imagenProducto);
            interfaz.appendChild(productoDiv);
            
        })
        //Muestra el montoTotal en carrito de compras.
        const parrafoMontoTotal = document.createElement('P');
        parrafoMontoTotal.classList.add('parrafoMontoTotal')
        montoTotal = sumarProductosCarrito();
        parrafoMontoTotal.textContent = `Monto Total: $${montoTotal.toLocaleString()} pesos Colombianos`;
        interfaz.appendChild(parrafoMontoTotal)

        //Boton de comprar en interfaz de carrito de compras.
        const botonComprar = document.createElement('BUTTON');
        botonComprar.classList.add('botonComprar')
        botonComprar.textContent = `Comprar`
        botonComprar.addEventListener('click', enviarDatos )
        interfaz.appendChild(botonComprar);
}

function sumarProductosCarrito(){
    let total = 0
    carritoDeCompra.forEach(producto => {
        let precio = parseInt(producto.precio) * producto.cantidad
        total = total + precio;
    });
    return total;
}

function agregarCantidad(producto){

    const index = carritoDeCompra.indexOf(producto);
    carritoDeCompra[index].cantidad ++;
    actualizarInterfazCarritoCompra();
}
function restarCantidad(producto){
    const index = carritoDeCompra.indexOf(producto);
    carritoDeCompra[index].cantidad --;
    if(carritoDeCompra[index].cantidad < 1){
        borrarProductoDeCarritoDeCompra(producto.id, producto);
    }
    
    actualizarInterfazCarritoCompra();
}
function crearModal(){
    esconderScroll();
    
    //este codigo solo se ejecuta una vez..
    const modal = document.createElement('DIV');
    modal.classList.add('modal', 'modalCarrito');
    ocultarHeader();
    modal.addEventListener('click', ocultarModal)

    const body = document.querySelector('body');
    const carritoDeCompras = document.createElement('DIV')
    carritoDeCompras.id = 'carritoDeCompras'
    carritoDeCompras.innerHTML = `
    <div class="contenedorCarritoCompras"></div>
    `
    modal.appendChild(carritoDeCompras);
    body.appendChild(modal);
}

function crearBotonBorrar(contenedor, id, objetoProducto){
    //crear boton en el contenedor
    const botonBorrar = document.createElement('BUTTON');
    botonBorrar.classList.add('botonBorrar')
    botonBorrar.textContent = 'Elimina el producto'
    contenedor.appendChild(botonBorrar);

    //borrar el producto en el carrito que tenga el id pasado...
    botonBorrar.addEventListener('click', function(){
        carritoDeCompra.forEach( producto =>{
            if(producto.id === id){
            const index = carritoDeCompra.indexOf(producto);
            carritoDeCompra.splice(index, 1)
            //Formateo el objeto a por default... para que al agregarlo al carrito no tenga problemas..
            delete objetoProducto.cantidad;
            actualizarInterfazCarritoCompra();

            }
        })
    })

}

function borrarProductoDeCarritoDeCompra(id, objetoProducto){
    carritoDeCompra.forEach( producto =>{
        if(producto.id === id){
        const index = carritoDeCompra.indexOf(producto);
        carritoDeCompra.splice(index, 1)
        //Formateo el objeto a por default... para que al agregarlo al carrito no tenga problemas..
        delete objetoProducto.cantidad;
        actualizarInterfazCarritoCompra();

        }
    })
}
function ocultarModal(event){
    
const modal = document.querySelector('.modalCarrito')
    eventClass = event.target.closest('.contenedorCarritoCompras')
    if(!eventClass){
        mostrarScroll();
        /* modal.classList.add('ocultar'); */
        modal.remove();
        mostrarHeader();
        
    }
}
function mostrarScroll(){
    const contenedor = document.querySelector('body');
    contenedor.style.overflowY = 'scroll';
}
function esconderScroll(){
    const contenedor = document.querySelector('body');
    contenedor.style.overflowY = 'hidden';
    
}
//Termina carrito compras//


//Agrega evento al menuHamburgesa
function eventoMenu(){
    menu = document.querySelector('.hamburgerMenu');
    menu.addEventListener('click', function(){
        mostrarModal();
        
    })
}
//Muestra modal.
function mostrarModal(){
    ocultarHeader();
    botonCerrar = document.querySelector('.icono')
    modal = document.querySelector('.modal');
    modal.classList.remove('ocultar');
    modal.addEventListener('click', ocultarModalAndMostrarHeader)
    /* botonCerrar.addEventListener('click', function(){
        ocultarModalAndMostrarHeader(event, '.icono');
    }) */
}
//Oculta el modal y header presionando afuera del menu...
function ocultarModalAndMostrarHeader(event){
    eventClass = event.target.closest('.menu')
    if(!eventClass){
        modal.classList.add('ocultar');
        mostrarHeader();
        modal.removeEventListener('click', ocultarModalAndMostrarHeader)
        
    }
}
//Subrayar pagina actual
function subrayarPaginaActual(){
    paginaActual = obtenerPaginaActual();
    menuCampos = document.querySelectorAll('.menuCampo a');
    menuCampos.forEach( menuCampo =>{
        if(menuCampo.attributes.href.value === paginaActual ){
            menuCampo.classList.add('subrayarAmarillo');
        }
    })
    
}

function obtenerPaginaActual(){
    const paginaActual = window.location.pathname;
    return paginaActual;
}
function ocultarHeader(){
    header = document.querySelector('header');
    header.classList.add('ocultar');
}
function mostrarHeader(){
    header = document.querySelector('header');
    header.classList.remove('ocultar');
}
//agregar posicion fixed en navegacion.
function agregarFixed(){
    header = document.querySelector('.header');
    sectionReferencia = document.querySelector('.nombre-pagina');

    document.addEventListener('scroll', function(){
        let scrollNumber = sectionReferencia.getBoundingClientRect().top
        if(scrollNumber < 1){
            header.classList.add('positionFixed');
        }
        else{
            header.classList.remove('positionFixed');
        }
    })
}
//Agrega evento a inputs Radio.
function eventoInputs(){
    inputBoton = document.querySelectorAll("[name='user[contacto]']")
    inputBoton.forEach(function(input){
        input.addEventListener('click', function(e){
            target = e.target.id;
            mostrar(target);
        })
    });
}
//Muestra el contacto seleccionado..
function mostrar (id){
    div = document.querySelector('.contactoDiv');
    if(id === 'byTelefono'){
        div.innerHTML = `
        <label for="telefono">Numero de Celular</label>
        <input id="telefono" type="tel" placeholder="Tu numero de celular" name="user[telefono]">
        `
    }
    if(id === 'byEmail'){
        div.innerHTML = `
        <label for="email">Correo</label>
        <input id="email" type="email" placeholder="Tu correo electronico" name="user[email]">
        `
    }
    

}