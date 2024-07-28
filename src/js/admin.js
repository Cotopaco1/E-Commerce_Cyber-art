import {activar_evento_submit_by_id,
    capturar_y_verificar_datos_de_formulario,
    crear_alerta_de_cargando,
    eliminar_alerta_de_cargando
} from './helper.js';

document.addEventListener('DOMContentLoaded', function(){
    
    iniciarApp();
})
//Globales
let productos_json = {};
let pedidos_json = {}; 
let filtros = {
    fecha : '',
    estado:'todos'
}
/* window.respuestaGlobal = {}; */
//Cierro globales
function iniciarApp(){

    mostrarDatosResumen();
    eventosBotones();
    eventos_botones_menu();
    
}


//modal actualizar

//Termina modal actualizar

//Botones menu
function eventos_botones_menu(){
    document.querySelector('#home').addEventListener('click', actualizarPagina)
    document.querySelector('#productos').addEventListener('click', mostrarProductos)
    document.querySelector('#nuevo_producto').addEventListener('click', mostrar_crear_producto);
    document.querySelector('#pedidos').addEventListener('click', mostrarPedidos);
}
//Termina botones menu

//Botones footer admin
function eventosBotones(){
    document.querySelector('#boton_pedidos').addEventListener('click', mostrarPedidos);
    document.querySelector('#boton_productos').addEventListener('click', mostrarProductos);
    document.querySelector('#menu_hamburgesa').addEventListener('click', mostrarMenu)
}
//Termina botones foter admin

//comienza evento menu hamburgesa
function mostrarMenu(e){
    const menu = document.querySelector('.menu_administrador');
    menu.classList.toggle('mostrar_flex');
    return;
    
}

//termina menu hamburgeesa

//Empieza pedidos

function mostrarPedidos(){
    cambiarTitulo('Pedidos');
   
    crearTabla(['id','fecha','estado','monto_total','actions'])
    solicitud_get_por_url('/api/admin/get_pedidos')
    /* .then(llenarAndMostrarTablaPedidos);
    insertar_buscador(); */
    .then(function(datos){
        pedidos_json = datos.respuesta;
        llenarAndMostrarTablaPedidos();
    });
    insertar_buscador();
   
}
function insertar_buscador(){
    const contenido = document.querySelector('.contenido');
    const div = document.createElement('DIV');
    div.classList.add('div_buscador');
    div.innerHTML = `
    <label htmlFor="buscador">Filtrar por fecha    <input class="input" type="date" id="buscador" /></label>
    <select name="estado" id="buscador_estado">
        <option value="todos" selected>Todos</option>
        <option value="pendiente">Pendiente</option>
        <option value="completado">Completado</option>
    </select>
    `
    contenido.prepend(div);
    //buscador fecha
    const buscador = document.getElementById('buscador');
    buscador.addEventListener('input', evento_buscador_fecha)
    //buscador estado
    const buscador_estado = document.getElementById('buscador_estado');
    buscador_estado.addEventListener('change', evento_buscador_estado)
}
// Peticion where y llena pedidos_json con la respuesta;
function evento_buscador_fecha(e){
     filtros.fecha = e.target.value;
    //vaciar body tabla
    /* document.getElementById('tbody').innerHTML = `` */
    //consultar datos
    solicitud_get_por_url(`/api/admin/get_pedidos_filtrados?fecha=${filtros.fecha}&estado=${filtros.estado}`)
    .then((datos)=>{
        pedidos_json = datos.pedidos;
        llenarAndMostrarTablaPedidos()
    })
    
}
function evento_buscador_estado(e){
    filtros.estado = e.target.value;
    //vaciar body tabla
    /* document.getElementById('tbody').innerHTML = `` */
    //consultar datos
    solicitud_get_por_url(`/api/admin/get_pedidos_filtrados?fecha=${filtros.fecha}&estado=${filtros.estado}`)
    .then((datos)=>{
        pedidos_json = datos.pedidos;
        llenarAndMostrarTablaPedidos()
    })
    
}
//Lo llena con el objeto global json_pedidos;
function llenarAndMostrarTablaPedidos(){
    const tabla = document.getElementById('tbody');
    tabla.innerHTML = ``;
    /* pedidos_json = datos.respuesta; */
    let idAnterior = 0;
    pedidos_json.forEach(pedido=>{
        const {id, fecha, status, total} = pedido;
        if(id === idAnterior) return;
        const tr = document.createElement('TR');
        /* const td = document.createElement('TD'); */
        tr.innerHTML = `
        <td><p>${id}</p></td>
        <td><p>${fecha}</p></td>
        <td><p class="${status} estado" data-id="${id}" data-estado="${status}">${status}</p></td>
        <td><p>$<span>${parseInt(total).toLocaleString()}</span></p></td>
        <td>
        <button data-id="${id}" class="boton-ver">Ver</button>
        </td>
        `
        tabla.appendChild(tr);
        idAnterior = id;
    })
    
    evento_botones_pedidos_action();
}

function evento_botones_pedidos_action(){
    document.querySelectorAll('.boton-ver').forEach(boton=>{
        boton.addEventListener('click',mostrar_ver_pedido)
    })
    document.querySelectorAll('[data-estado]').forEach(elemento=>{
        elemento.addEventListener('dblclick', cambiar_estado)
    })
}
async function cambiar_estado(e){
    const estado = e.target.dataset.estado;
    const id = e.target.dataset.id;
    const datos = new FormData();
    datos.append('id',id);
    datos.append('status',estado);
    datos.append('fecha',filtros.fecha);
    datos.append('estado',filtros.estado);
    //peticion para cambiar de estado el pedido.
    const url = '/api/admin/pedido/actualizar_estado';
    const options = {
        method: 'POST',
        body: datos
    }
    try {
        const resultado = await fetch(url,options);
        const respuesta = await resultado.json()
        if(respuesta.exito){
            pedidos_json = respuesta.pedidos
            llenarAndMostrarTablaPedidos();
        }else{
            console.log(respuesta);
        }
    } catch (error) {
        console.log(error)
    }
}
function mostrar_ver_pedido(e){
    crearModal();
    const idPedido = e.target.dataset.id;
    const pedido = pedidos_json.find(function(target){
        return target.id === idPedido;        
    });

    insertar_al_modal_pedido_ver(pedido);
    //creamos modal
    //INSERTAMOS AL MODAL

}
function insertar_al_modal_pedido_ver(pedido){

    const {id, fecha, status, nombre, email, telefono, direccion,
        informacion_adicional,total,metodo_pago, cedula
    } = pedido
    const modal = document.querySelector('.contenido_modal');

    modal.innerHTML= `
    <div class="producto_div pedido_div">
            <div class="pedido_div_uno">
                <p>ID Pedido: <span>${id}</span></p>
                <p>Fecha: <span>${fecha}</span></p>
                <p>Estado: <span>${status}</span></p>
        
            </div>
            <div class="pedido_div_dos">
                <p>Nombre: <span>${nombre}</span></p>
                <p>Correo: <span>${email}</span></p>
                <p>telefono: <span>${telefono}</span></p>
                <p>Cedula: <span>${cedula}</span></p>
            </div>
            <div class="div_pedido_tres">
                <p>Direccion: <span>${direccion}</span></p>
            </div>
            <div class="lista_nombre_productos"></div>
            <div> <p>Informacion Adicional: <span>${informacion_adicional}</span></p></div>
            
            <div class="div_pedido_footer">
                <p>Estado: <span>${status}</span></p>
                <p>Metodo de pago: <span>${metodo_pago}</span></p>
                </div>
            <p>Total: <span>$${parseInt(total).toLocaleString()}</span></p>

        <div class="producto_div_footer">
            <button class="boton-eliminar" id="cancelar_boton">Volver</button>
        </div>
    </div>
    `
    /* if(disponible === 1){
        const optionSelected = document.getElementById('disponible');
        optionSelected.setAttribute('selected')
        
    } */
    insertar_nombre_productos(id);
    const boton_cancelar = document.getElementById('cancelar_boton');
    boton_cancelar.addEventListener('click', eliminarModal)
}
function insertar_nombre_productos(id){
    const div = document.querySelector('.lista_nombre_productos');
    const productos = pedidos_json.filter(producto=>{
        /* console.log(producto.id === 1); */
        return producto.id === id;
    })
    productos.forEach(producto=>{
        const parrafo = document.createElement('P');
        parrafo.innerHTML = `<span>${producto.productoNombre}</span> x${producto.productoCantidad}`
        div.appendChild(parrafo);
    })
    /* console.log(id);
    console.log(pedidos_json); */
    /* console.log(productos); */

}

//termina pedidos

//Empieza crear productos
function mostrar_crear_producto(){
    insertar_formulario_crear();
    cambiarTitulo('Crear Producto')
    // evento para crear producto
    document.querySelector('.formulario').addEventListener('submit', evento_formulario_crear)
}
async function evento_formulario_crear(event){
    event.preventDefault();
    const formData = new FormData(event.target);
        /* const prueba = [...formData];
        console.log(prueba);
        return; */
    //Si no esta vacio, entonces hacemos fetch para actualizar.
    try {

        const url = `${location.origin}/api/admin/crear_producto`;
        const options = {
            method: 'post',
            body: formData
        }
        crear_alerta_de_cargando();
        const resultado = await fetch(url, options);
        const respuesta = await resultado.json();
        eliminar_alerta_de_cargando();
        if(respuesta.resultado){
            Swal.fire({
                title: "Buen trabajo!",
                text: `${respuesta.mensaje}`,
                icon: "success",
                fontsize: '5rem',
                }).then(mostrarProductos)
        }else{
            console.log(respuesta);
            const divRespuesta = document.getElementById('mensaje_error')
            divRespuesta.innerHTML = ``;
            respuesta.alertas.forEach(alerta=>{
                const parrafo = document.createElement('P')
                parrafo.textContent = alerta;
                divRespuesta.appendChild(parrafo);
            })
            /* divRespuesta.innerHTML = `
            
            <p>${respuesta.alertas[0]}</p>
            ` */
        }
        

        
    } catch (error) {
        console.log(error)
    }
}
function insertar_formulario_crear(){
    const contenido = document.querySelector('.contenido')
    contenido.innerHTML = `
     <form class="producto_div formulario crear_producto" id="formulario" enctype="multipart/form-data">
                <div class="informacion_producto ">
                    <label htmlFor="nombre_editar">Nombre: <input name="nombre" id="nombre_editar" type="text" /></label>
                    <label >Precio: <input name="precio" id="precio_editar" type="text"/></label>
                    <label htmlFor="">
                    Disponible:
                    <select id="select_disponible" name="disponible">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    </label>
                    <label htmlFor="">Tamaño: <input type="text" name="size" /></label>
                </div>
           
            <div class="div_producto_dos">
                <input type="file" name="imagen" />
                 <label for="descripcion" >Descripcion:</label>
                  <textarea name="descripcion" id="descripcion" placeholder="Escribe la descripcion" ></textarea>
            </div>
        <div id="mensaje_error"></div>
        <div class="producto_div_footer">
            <input type="submit" value="Crear producto" id="crear_producto" class="boton-ver" />
            
        </div>
    </form>
    `

}

//Termina crear productos

//Empieza Productos

function evento_botones_action(){
    document.querySelectorAll('.boton-editar').forEach(boton=>{
        boton.addEventListener('click', mostrar_editar_producto)
    });
    document.querySelectorAll('.boton-ver').forEach(boton=>{
        boton.addEventListener('click', mostrar_ver_producto)
    })
    document.querySelectorAll('.boton-eliminar').forEach(boton=>{
        boton.addEventListener('click', eliminar_producto)
    })
    
}
    //Empiza eliminar
        //Realizar un fetch post, para eliminar un producto.
async function eliminar_producto(event){
    const id = event.target.dataset.id;
    const data = {
        id: id
    }
    const url = '/api/admin/eliminar_producto';
    try {
        const options = {
            method: 'POST',
            body: JSON.stringify(data)
        }
        const resultado = await fetch(url, options);
        const respuesta = await resultado.json();
        if(respuesta.resultado){
            Swal.fire({
                title: "Buen trabajo!",
                text: `${respuesta.mensaje}`,
                icon: "success",
                fontsize: '5rem',
              }).then(mostrarProductos)
        }else{
            Swal.fire({
                title: "Error!",
                text: `${respuesta.mensaje}`,
                icon: "error",
                fontsize: '5rem',
              })
        }


        
    } catch (error) {
        
    }
    //obtener id del producto a eliminar
    //Enviar fetch
    //si respuesta es true, actualizar productos.
    //Si es falso, mostrar alerta de error.

} 
    //Termina eliminar

    //Empieza ver
function mostrar_ver_producto(e){
    const idProducto = e.target.dataset.id;
    const producto = productos_json.find(function(target){
        return target.id === idProducto;        
    });

    crearModal();
    insertar_al_modal_producto_ver(producto);
}
function insertar_al_modal_producto_ver(producto){
    const {descripcion, disponible, id, imagen, nombre, precio, size} = producto
    const modal = document.querySelector('.contenido_modal');
    modal.innerHTML= `
    <div class="producto_div">
            <div class="div_producto_uno">
                <div class="imagen_producto_div">
                    <img src="/img/productos/${imagen}" alt="imagenProducto" />
                    
                </div>
                <div class="informacion_producto">
                    <p >ID: <span>${id}</span></p>
                    <p>Nombre: <span>${nombre}</span></p>
                    <p>Precio: <span>${precio}</span></p>
                    <p>Tamaño: <span>${size}</span></p>
                    <label htmlFor="">
                    Disponible:
                    <select disabled id="select_disponible" name="disponible">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    </label>
                </div>
            </div>
            <div class="div_producto_dos">
                 <p for="descripcion" ><span>Descripcion:</span></p>
                  <textarea name="descripcion" id="descripcion" disabled>${descripcion}</textarea>
            </div>
        <div class="producto_div_footer">
            <button class="boton-eliminar" id="cancelar_boton">Volver</button>
        </div>
    </div>
    `
    /* if(disponible === 1){
        const optionSelected = document.getElementById('disponible');
        optionSelected.setAttribute('selected')
        
    } */
   const select = document.getElementById('select_disponible');
   select.value = disponible;
    const boton_cancelar = document.getElementById('cancelar_boton');
    boton_cancelar.addEventListener('click', eliminarModal)
}
    //Termina ver

    //Empieza editar
function mostrarProductos(){
    cambiarTitulo('Productos')
    crearTabla(['Imagen','Nombre','Precio','Actions']);
    solicitud_get_por_url('/api/admin/get_cuadros_where_no_deleted')
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
function crearTabla(args = []){
    const contenido = document.querySelector('.contenido')
    contenido.innerHTML = `
    <table class="tablaResumen id="tabla">
    <thead>
            <tr class="trHead">
                
            </tr>
        </thead>
        <tbody id="tbody">
        
        </tbody>
    </table>
    `
    const trHead = document.querySelector('.trHead');
    args.forEach(mensaje=>{
        const td = document.createElement('TD');
        td.textContent = mensaje;
        trHead.appendChild(td);
    })


}
function llenarAndMostrarTablaProductos(datos){
    const tabla = document.getElementById('tbody');
    productos_json = datos;
    datos.forEach(producto=>{
        const {descripcion, disponible, id, imagen, nombre, precio, size} = producto;
        const tr = document.createElement('TR');
        /* const td = document.createElement('TD'); */
        tr.innerHTML = `
        <td><img src="/img/productos/${imagen}" alt="imagenProducto" /></td>
        <td><p>${nombre}</p></td>
        <td><p>$<span>${parseInt(precio).toLocaleString()}</span></p></td>
        <td>
            <div class="actions">
                <button data-id="${id}" class="boton-editar">Editar</button>
                <button data-id="${id}" class="boton-ver">Ver</button>
                <button data-id="${id}" class="boton-eliminar">Eliminar</button>
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

function mostrar_editar_producto(e){
    const idProducto =e.target.dataset.id;
    const producto = productos_json.find(function(target){
        return target.id === idProducto;        
    });

    crearModal();
    insertar_al_modal_producto(producto);
    //crear evento submit para el formulario.
    document.querySelector('.formulario').addEventListener('submit', evento_formulario_actualizar)
        
}

async function evento_formulario_actualizar(event){
   /*  const datos = capturar_y_verificar_datos_de_formulario(event); */
    //Si algun valor de un input es vacio entonces:
        /* if(!datos){
            const mensaje_div = document.getElementById('mensaje_error');
            const mensajeAnterior = mensaje_div.firstChild;
            if(mensajeAnterior) mensajeAnterior.remove();
            const mensaje = document.createElement('P');
            mensaje.textContent = 'Porfavor rellena todos los campos.'
            mensaje_div.appendChild(mensaje);
            return; 
        }  */
            event.preventDefault();
            const formData = new FormData(event.target);
            /* const prueba = [...formData];
            console.log(prueba);
            return; */

        //Si no esta vacio, entonces hacemos fetch para actualizar.
        try {

            /* const json = JSON.stringify(datos); */

            const url = `${location.origin}/api/admin/actualizar_producto`;
            const options = {
                method: 'post',
                body: formData
            }
            crear_alerta_de_cargando();
            const resultado = await fetch(url, options);
            const respuesta = await resultado.json();
            eliminar_alerta_de_cargando();
            if(respuesta.resultado){
                Swal.fire({
                    title: "Buen trabajo!",
                    text: `${respuesta.mensaje}`,
                    icon: "success",
                    fontsize: '5rem',
                  }).then(eliminar_modal_and_mostrar_productos)
            }else{
                
                const divRespuesta = document.getElementById('mensaje_error')
                divRespuesta.innerHTML = ``;
                respuesta.alertas.forEach(alerta=>{
                    const parrafo = document.createElement('P')
                    parrafo.textContent = alerta;
                    divRespuesta.appendChild(parrafo);
                })
                /* divRespuesta.innerHTML = `
                
                <p>${respuesta.alertas[0]}</p>
                ` */
            }
            

            
        } catch (error) {
            console.log(error)
        }
        
}
function eliminar_modal_and_mostrar_productos(){
    eliminarModal()
    mostrarProductos()

}
function insertar_al_modal_producto(producto){
    const {descripcion, disponible, id, imagen, nombre, precio, size} = producto
    const modal = document.querySelector('.contenido_modal');
    modal.innerHTML= `
    <form class="producto_div formulario" id="formulario" enctype="multipart/form-data">
        
            <div class="div_producto_uno">
                <div class="imagen_producto_div">
                    <img src="/img/productos/${imagen}" alt="imagenProducto" />
                    
                </div>
                <div class="informacion_producto">
                    <label htmlFor="">ID: ${id}</label>
                    <input type="hidden" name="id" value="${id}" />
                    <label htmlFor="nombre_editar">Nombre: <input name="nombre" id="nombre_editar" type="text" value="${nombre}" /></label>
                    <label >Precio: <input name="precio" id="precio_editar" type="text" value="${precio}" /></label>
                    <label htmlFor="">
                    Disponible:
                    <select id="select_disponible" name="disponible">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    </label>
                    <label htmlFor="">Tamaño: <input type="text" name="size" value="${size}" /></label>
                </div>
            </div>
            <div class="div_producto_dos">
                <input type="file" name="imagen" value="${imagen}" />
                 <label for="descripcion" >Descripcion:</label>
                  <textarea name="descripcion" id="descripcion" >${descripcion}</textarea>
            </div>
        <div id="mensaje_error"></div>
        <div class="producto_div_footer">
            <input type="submit" value="Actualizar Datos" id="actualizar_datos" class="boton-ver" />
            <button class="boton-eliminar" id="cancelar_boton">Cancelar</button>
        </div>
    </form>
    `
    /* if(disponible === 1){
        const optionSelected = document.getElementById('disponible');
        optionSelected.setAttribute('selected')
        
    } */
   const select = document.getElementById('select_disponible');
   select.value = disponible;

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
    const eventClass = event.target.closest('.producto_div')
    if(!eventClass){
        eliminarModal();
    }
}
function eliminarModal(){
    const modal = document.querySelector('.modal')
    modal.remove();
       
}
    //Termina editar

//Termina productos


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
        
        const url = '/api/admin/get_info_resumen'

        const resultado = await fetch(url);
        const $respuesta = await resultado.json();
        return $respuesta;
        
    } catch (error) {
        console.log(error)
    }

}
function actualizarPagina(){
    location.reload();
}
//Termina resumen