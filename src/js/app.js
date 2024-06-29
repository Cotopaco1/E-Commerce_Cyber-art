const carritoDeCompra = []
document.addEventListener('DOMContentLoaded', function(){
iniciarApp();

})

function iniciarApp(){
    eventoInputs(); //Inputs radio
    eventoMenu(); //Menu header
    agregarFixed(); //Menu Header
    subrayarPaginaActual(); //menu Header
    eventoCarritoUserBoton(); //menu Header
    eventoCuadros();
    consultarApiCuadros();
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

    carritoDeCompra.push(cuadro);
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

    if(!carritoDeCompra.length >= 1){
        const parrafo = document.createElement('P');
        parrafo.classList.add('parrafoCarritoVacio');
        parrafo.textContent = 'El carrito esta vacio ...'
        interfaz.appendChild(parrafo);
        return
    }
        

        while(interfaz.firstChild){
            console.log(interfaz.firstChild)
            interfaz.removeChild(interfaz.firstChild);
        }
        carritoDeCompra.forEach(producto =>{
            const {id, nombre, precio, descripcion, size, disponible, imagen} = producto;
            
            const productoDiv = document.createElement('DIV');
            productoDiv.classList.add("interfazCampo");

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = nombre;
            nombreProducto.classList.add('nombre-producto')
            const precioProducto = document.createElement('P');
            precioProducto.textContent = precio.toLocaleString('es-ES');
            precioProducto.classList.add('precio-producto')

            

            const divInfo = document.createElement('DIV');
            divInfo.classList.add('infoProductoCarrito');
            divInfo.appendChild(nombreProducto)
            divInfo.appendChild(precioProducto)
            crearBotonBorrar(divInfo, id );

            const imagenProducto = document.createElement('IMG');
            imagenProducto.src = `/img/${imagen}`;

            productoDiv.appendChild(divInfo);
            productoDiv.appendChild(imagenProducto);
            interfaz.appendChild(productoDiv);
            
        })

    
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

function crearBotonBorrar(contenedor, id){
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
                actualizarInterfazCarritoCompra();

            }
        })
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