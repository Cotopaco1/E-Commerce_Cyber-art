document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
})
//Globales
let productos_json = {}; 

//Cierro globales
function iniciarApp(){

    mostrarDatosResumen();
    eventosBotones();
    
}



//Botones footer admin

function eventosBotones(){
    document.querySelector('#boton_pedidos').addEventListener('click', mostrarPedidos);
    document.querySelector('#boton_productos').addEventListener('click', mostrarProductos);
}

function mostrarPedidos(){
    //Crear tabla para mostrar pedidos.
    //Solicitar informacion de los pedidos.
    //Insertar la informacion de los pedidos a la tabla
    //Mostrar la tabla creada en pantalla
    console.log('mostrando productos...')
}

function mostrarProductos(){
    cambiarTitulo('Productos')
    crearTabla();
    solicitud_get_por_url('http://localhost:3000/api/cuadros/getcuadros')
    .then(llenarAndMostrarTablaProductos);
    //Crear tabla para mostrar productos.
    //Solicitar informacion de los productos.
    //Insertar la informacion de los productos a la tabla
    //Mostrar la tabla creada en pantalla
    //crear evento al boton editar
    //crear evento al boton ver
    //crear evento al boton eliminar.
}
function cambiarTitulo(titulo){
    const tituloDiv = document.querySelector('.titulo_app');
    tituloDiv.innerHTML = `<h1>${titulo}</h1>`
}
function crearTabla(){
    const contenido = document.querySelector('.contenido')
    contenido.innerHTML = `
    <table class="tablaResumen id="tabla">
    <thead>
            <tr>
                <td>Imagen</td>
                <td>Nombre</td>
                <td>Precio</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody id="tbody">
        
        </tbody>
    </table>
    `
}
function llenarAndMostrarTablaProductos(datos){
    const tabla = document.getElementById('tbody');
    productos_json = datos;
    datos.forEach(producto=>{
        const {descripcion, disponible, id, imagen, nombre, precio, size} = producto;
        const tr = document.createElement('TR');
        /* const td = document.createElement('TD'); */
        tr.innerHTML = `
        <td><img src="/img/${imagen}" alt="imagenProducto" /></td>
        <td><p>${nombre}</p></td>
        <td><p>$<span>${precio}</span></p></td>
        <td>
            <div class="actions">
                <button data-id="${id}" class="boton-editar">Editar</button>
                <button class="boton-ver">Ver</button>
                <button class="boton-eliminar">Eliminar</button>
            </div>
        </td>
        `
        tabla.appendChild(tr);

    })
    evento_botones_action();
}
async function solicitud_get_por_url(url){

    try {
        /* url = 'http://localhost:3000/api/cuadros/getCuadros'; */
        const resultado = await fetch(url);
        const respuesta = await resultado.json();
        return respuesta;
        
    } catch (error) {
        console.log(error)
    }
}
function evento_botones_action(){
    document.querySelectorAll('.boton-editar').forEach(boton=>{
        boton.addEventListener('click', mostrar_editar_producto)
    });
}
function mostrar_editar_producto(e){
    const idProducto =e.target.dataset.id;
    const producto = productos_json.find(function(target){
        return target.id === idProducto;
        /* if(target.id === idProducto) return target; */
        
    });

    crearModal();
    insertar_al_modal_producto(producto);
    
    
}

function insertar_al_modal_producto(producto){
    const {descripcion, disponible, id, imagen, nombre, precio, size} = producto
    const modal = document.querySelector('.contenido_modal');
    modal.innerHTML= `
    <form class="producto_div formulario">
        
            <div class="div_producto_uno">
                <div class="imagen_producto_div">
                    <img src="/img/${imagen}" alt="imagenProducto" />
                    
                </div>
                <div class="informacion_producto">
                    <label htmlFor="">ID: ${id}</label>
                    <label htmlFor="nombre_editar">Nombre: <input name="nombre" id="nombre_editar" type="text" value="${nombre}" /></label>
                    <label >Precio: <input name="precio" id="precio_editar" type="text" value="${precio}" /></label>
                    <label htmlFor="">
                    Disponible:
                    <select name="disponible">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    </label>
                    <label htmlFor="">Tamaño: <input type="text" value="${size}" /></label>
                </div>
            </div>
            <div class="div_producto_dos">
                <input type="file" value="${imagen}" />
                 <label for="descripcion" >Descripcion:</label>
                  <textarea name="descripcion" id="descripcion" >${descripcion}</textarea>
                
                
            </div>
        

        <div class="producto_div_footer">
            <input type="submit" value="Actualizar Datos" class="boton-ver" />
            <button class="boton-eliminar" id="cancelar_boton">Cancelar</button>
        </div>
    </form>
    `
    const boton_cancelar = document.getElementById('cancelar_boton');
    boton_cancelar.addEventListener('click', eliminarModal)

}

function crearModal(){
    //este codigo solo se ejecuta una vez..
    const modal = document.createElement('DIV');
    modal.classList.add('modal');
    modal.innerHTML = `
    <div class="contenido_modal"></div>
    `
    modal.addEventListener('click', evento_para_cerrar_modal)
    const body = document.querySelector('body');

    body.appendChild(modal);
}
function evento_para_cerrar_modal(event){
    eventClass = event.target.closest('.producto_div')
    if(!eventClass){
        eliminarModal();
    }
}
function eliminarModal(){
    const modal = document.querySelector('.modal')
    modal.remove();
       
}

//Termina botones foter admin

//Resumen
function mostrarDatosResumen(){
   getDatosResumen()
   .then(imprimirDatosResumen);
}
function imprimirDatosResumen(datos){
    const spanNumeroPedidos = document.querySelector('#numero_pedidos span')
    spanNumeroPedidos.textContent = datos.respuesta.cantidad_pedidos

    const spanNumeroProductos = document.querySelector('#numero_productos span')
    spanNumeroProductos.textContent = datos.respuesta.cantidad_productos

    const spanNumeroUsuarios = document.querySelector('#numero_usuarios span')
    spanNumeroUsuarios.textContent = datos.respuesta.cantidad_usuarios


}
async function getDatosResumen(){

    try {
        
        const url = 'http://localhost:3000/api/admin/get_info_resumen'

        const resultado = await fetch(url);
        const $respuesta = await resultado.json();
        return $respuesta;
        
    } catch (error) {
        console.log(error)
    }

}
//Termina resumen