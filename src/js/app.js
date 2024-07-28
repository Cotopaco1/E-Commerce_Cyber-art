let validado = false;
let errorInput = true;
let formularioDatos;
let montoTotal = 0;
let carritoDeCompra = {
    productos: [],
    montoTotal: function(){
        let total = 0
        this.productos.forEach(producto=>{
            total = total + (parseInt(producto.precio)* producto.cantidad)
            
        })
        return total;
    }
}
const usuario = {
    id : null,
    nombre : '',
    apellidos: '',
    datos_de_contacto: {
        telefono: '',
        email: '',
        direccion: '',
        ciudad: '',
        departamento: ''
    }
}


let metodo_pago = '';
let paso = 1;
const path = window.location.pathname;
document.addEventListener('DOMContentLoaded', function(){
iniciarApp();

})

function iniciarApp(){
    
    eventoInputs(); //Inputs radio
    eventoMenu(); //Menu header
    subrayarPaginaActual(); //menu Header
    eventoCarritoUserBoton(); //menu Header
    if(path === '/'){
        agregarFixed(); //Menu Header
        eventoCuadros();
        get_cuadros();
        obtener_carrito_session();
        evento_formulario_index();

    }
    if(path === '/carrito_de_compras'){
        obtener_carrito_session()
        .then(mostrarCarritoDeCompras)
        agregarFixed(); //Menu Header
        crearOpcionesEnSelected();
        /* recuperarCarritoCompras(); */
        tabs();
        mostrarSeccion();
        agregar_evento_botones_paginacion();
        inputMetodosDePago();
        formulario_evento();
        /* contenedorMetodoPagoEvento(); */
    }
    if(path === '/crear_cuenta'){
        eventos_crear_cuenta();
        ocultarHeader();
        return
    }
    if(path === '/login'){
        eventos_login();
        ocultarHeader();
        return
    }
    if(path === '/reenviar_email'){
        eventos_reenviar_confirmacion();
        ocultarHeader();
        return
    }
    if(path === '/recuperar_password'){
        eventos_recuperar_password();
        ocultarHeader();
        return
    }
    if(path === '/reestablecer_password'){
        ocultarHeader();
        const inputPassword = document.getElementById('password');
        if(!inputPassword) return;
        eventos_reestablecer_password();
        
        return
    }
    if(path === '/producto'){
        iniciar_producto();
    }
    if(path === '/productos'){
        obtener_carrito_session();
    }
    
}

//empieza producto
    function iniciar_producto(){
        //agregar evento a los bontes
        mostrar_producto();
        obtener_carrito_session();
        evento_regresar();
    }
    function evento_regresar(){
        document.querySelector('#back-arrow').addEventListener('click', (e)=>{
            e.preventDefault();
            window.location.href = document.referrer
        })
    }

    function mostrar_producto(){
        const url = `/api/get/cuadro?id=${get_queryString('id')}`
        peticion_get(url)
        .then(respuesta=>{
            if(respuesta.respuesta){
                const producto = respuesta.producto
                //mostrar el producto
                const disponibilidad = {
                    0: 'No disponible',
                    1: 'Disponible'
                }
                const div = document.querySelector('.producto_div');
                const {descripcion, disponible, id, imagen, nombre, precio, size} = producto;
                div.innerHTML = `
                <div class="producto_imagen">
                    <img src="/img/productos/${imagen}" alt="imagen-producto">
                </div>
                <div class="info">
                    <div class="producto_informacion">
                        <p class="nombre">${nombre}</p>
                        <p class="precio">$${parseInt(precio).toLocaleString()}</p>
                        <p class="descripcion"><span>Descripcion</span><br>${descripcion}</p>
                        <p class="size"><span>Tamaño:</span> ${size}</p>
                        <p class="disponible">${disponibilidad[disponible]}</p>
                    </div>
                    <div class="acciones">
                        <button  class="boton-accion btn-agregar">Agregar al carrito</button>
                        <button  class="boton-accion btn-comprar">Comprar ahora</button>
                    </div>
                `
                evento_botones_accion(producto);
            }

        })
        
    }
    async function obtener_carrito_session(){
        const respuesta = await peticion_get('/api/session/get_carrito')
        if(respuesta.resultado){
            carritoDeCompra.productos = respuesta.productos;
        }
        
    }
    async function peticion_get(url){
        try {
            const resultado = await fetch(url);
            const respuesta = await resultado.json();
            return respuesta;
        } catch (error) {
            console.log(error)
        }
    }
    function get_queryString(param){
        const queryString = new URLSearchParams(window.location.search);
        return queryString.get(param);
    }
    function evento_botones_accion(producto){
        document.querySelector('.btn-agregar').addEventListener('click',
            function(){
                agregar_al_carrito(producto)
                mostrarInterfazCarritoCompras();
            }
        )
        document.querySelector('.btn-comprar').addEventListener('click',
            function(){
                agregar_al_carrito(producto)
                guardar_carrito_compras_en_session()
                .then(window.location.href = "/carrito_de_compras");
            }
        )
    }
    function agregar_al_carrito(producto){
        const existe = carritoDeCompra.productos.find(productoCarrito=>{
            return productoCarrito.id === producto.id
        })
        if(existe){
            existe.cantidad++;
            return;
        }
        producto.cantidad = 1;
        carritoDeCompra.productos = [...carritoDeCompra.productos, producto];
        //funcion para agregar el carrito en la session del usuario;
    }

//Termina producto

//Empieza reestablecer_password
function eventos_reestablecer_password(){
    document.querySelectorAll('.input').forEach(input=>{
        input.addEventListener('input', verificar_input);
    })
    document.getElementById('recuperar_password').addEventListener('click', activar_evento_submit_en_crear_cuenta)

    document.getElementById('formulario').addEventListener('submit', reestablecer_password )


}
function reestablecer_password(event){
    event.preventDefault()
    const data = crear_objeto_con_datos_de_formulario(event)
    //verificar el objeto antes de mandarlo al back-end
    Object.entries(data).forEach(([key, value]) => {
        if(value === ''){
            crear_alerta_de_error_con_id(key)
        }
        
    });
    if(errorInput){
        resaltarErrores();
        return;
    }
    
    crear_alerta_de_cargando();
    solicitar_reestablecer_password(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_reestablecer_password);

}
async function solicitar_reestablecer_password(data){
    const urlParams = new URLSearchParams(window.location.search);
    const token_reset_password = urlParams.get('token')
    data.token_reset_password = token_reset_password ;
    if(!token_reset_password) return;

    try {
        const json = JSON.stringify(data)
        const url = '/api/login/reestablecer_password'
        const options = {
            method: 'POST',
            body: json
        }
        const resultado = await fetch(url, options);
        const respuesta = await resultado.json();

        return respuesta;

        
    } catch (error) {
        console.log(error)
    }


}
function validar_respuesta_reestablecer_password(respuesta){
    const divRespuesta = document.getElementById('respuesta_servidor')
    divRespuesta.innerHTML = '';
    if(respuesta.respuesta === 'exito'){
        Swal.fire({
            title: "Buen trabajo!",
            text: `${respuesta.mensaje}`,
            icon: "success",
            fontsize: '5rem',
          }).then(()=>{
            window.location.href = '/login'
          })
    }else{
        divRespuesta.innerHTML = `
        <p>${respuesta.mensaje}</p>
        `
    }
}


//Termina reestablecer_password


//Empieza recuperar_password
function eventos_recuperar_password(){
    document.querySelector('#email').addEventListener('input', verificar_input_login)
    document.querySelector('#recuperar_password').addEventListener('click', activar_evento_submit_en_crear_cuenta)
    document.querySelector('#formulario').addEventListener('submit', recuperar_password)
}

function recuperar_password(event){
//verificar value de los inputs.
    event.preventDefault()
    const data = crear_objeto_con_datos_de_formulario(event)
    //verificar el objeto antes de mandarlo al back-end
    Object.entries(data).forEach(([key, value]) => {
        if(value === ''){
            crear_alerta_de_error_con_id(key)
        }
        
    });
    if(errorInput){
        resaltarErrores();
        return;
    }
    crear_alerta_de_cargando();
    solicitar_recuperar_password(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_recuperar_password);
}
async function solicitar_recuperar_password(data){

    try {
        json = JSON.stringify(data)
        const url = '/api/login/recuperar_email';
        const options = {
            method: 'post',
            body: json
        }
        const resultado = await fetch(url,options)
        const respuesta = await resultado.json();
        return respuesta;
        
    } catch (error) {
        console.log(error);
    }

}
function validar_respuesta_recuperar_password(respuesta){
    const divRespuesta = document.getElementById('respuesta_servidor')
    divRespuesta.innerHTML = '';
    if(respuesta.respuesta === 'exito'){
        Swal.fire({
            title: "Correo enviado exitosamente!",
            text: `${respuesta.mensaje}`,
            icon: "success",
            fontsize: '5rem',
          }).then(()=>{
            window.location.href = '/login'
          })
    }else{
        divRespuesta.innerHTML = `
        <p>${respuesta.mensaje}</p>
        `
    }

}

//Termina recuperar_password

//Empiza reenviar_confirmacion

function eventos_reenviar_confirmacion(){
    document.querySelector('#email').addEventListener('input', verificar_input_login)
    document.querySelector('#boton_reenviar').addEventListener('click', activar_evento_submit_en_crear_cuenta)
    document.querySelector('#formulario').addEventListener('submit', reenviar_confirmacion)
}

function reenviar_confirmacion(event){
    //verificar value de los inputs.
    event.preventDefault()
    const data = crear_objeto_con_datos_de_formulario(event)
    //verificar el objeto antes de mandarlo al back-end
    Object.entries(data).forEach(([key, value]) => {
        if(value === ''){
            crear_alerta_de_error_con_id(key)
        }
        
    });
    if(errorInput){
        resaltarErrores();
        return;
    }
    crear_alerta_de_cargando();
    solicitar_reenviar_confirmacion(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_reenviar_email);
    
}
function validar_respuesta_reenviar_email(respuesta){
    const divRespuesta = document.getElementById('respuesta_servidor')
    divRespuesta.innerHTML = '';
    if(respuesta.respuesta === 'exito'){
        Swal.fire({
            title: "Correo reenviado exitosamente!",
            text: `${respuesta.mensaje}`,
            icon: "success",
            fontsize: '5rem',
          })/* .then(()=>{
            window.location.href = 'http://localhost:3000/login'
          }) */
    }else{
        divRespuesta.innerHTML = `
        <p>${respuesta.mensaje}</p>
        `
    }

}
async function solicitar_reenviar_confirmacion(data){
    
    try {

        const url = '/api/login/reenviar_email'
        const json = JSON.stringify(data);
        const options = {
            method: 'POST',
            body: json
        }
        
        const resultado = await fetch(url,options);
        const respuesta = await resultado.json();
        return respuesta;
        
    } catch (error) {
        console.log(error);
    }
}
//Termina reenviar_confirmacion

//Empieza /login
function eventos_login(){
    document.getElementById('formulario').addEventListener('submit', logIn_usuario)

    document.getElementById('boton_ingresar').addEventListener('click' , activar_evento_submit_en_crear_cuenta )

    document.querySelectorAll('.input').forEach(input=>{
        input.addEventListener('input', verificar_input_login )
    })

}

function logIn_usuario(event){
    //capturar los datos del formulario en un objeto.
    event.preventDefault()

    const data = crear_objeto_con_datos_de_formulario(event)
    //verificar el objeto antes de mandarlo al back-end
    Object.entries(data).forEach(([key, value]) => {
        if(value === ''){
            crear_alerta_de_error_con_id(key)
        }
        
    });
    //Si en el objeto data hay algun campo que este vacio entonces...
    if(errorInput){
        resaltarErrores()
        return
    }
    crear_alerta_de_cargando();
    enviar_solicitud_login(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_login)
    
    //enviar solicitud de login al backend.

}

function validar_respuesta_login(respuesta){

    if(respuesta.respuesta === 'exitoso'){
        if(respuesta.sesionInfo.admin === true){
            window.location.href = '/admin/home'
            return;
        }
        window.location.href = '/'
    }
    else{
        console.log(respuesta);
        if(respuesta.mensaje.includes('email')){
            crear_alerta_de_error_con_id('email',respuesta.mensaje,true)

        }else{

            crear_alerta_de_error_con_id('password',respuesta.mensaje,true)
        }
    }
}
async function enviar_solicitud_login(data){

    try {

        const json = JSON.stringify(data)

        const options = {
            method : 'POST',
            body : json
        }
        const url = '/api/login/login';

        const resultado = await fetch(url, options);
        const respuesta = await resultado.json()
        return respuesta;
        /* console.log(respuesta); */
        
    } catch (error) {
        console.log(error)
    }
}

function verificar_input_login(e){
    const value = e.target.value
    const errorAnterior = document.querySelector(`#error${e.target.id}`)
    const idInput = e.target.id;
    if(value === ''){
        crear_alerta_de_error_con_id(idInput);
        return
    }
    if(idInput === 'email'){
        const esValido = validarEmail(value)
        if(!esValido){
            crear_alerta_de_error_con_id(idInput, 'Woops! parece que tu email no es valido', true)
            return
        }
    }
    if(errorAnterior){
        errorAnterior.remove()
    }
    const existeOtroError = document.querySelector('.errorInput')
    if(existeOtroError){
        return
    }
    
    errorInput = false;
}
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

//Termina /login

//Empieza crear_cuenta
    //validacion del front-end con los datos..
function eventos_crear_cuenta(){
    const formulario = document.getElementById('formulario');
    formulario.addEventListener('submit', crear_cuenta)
    const boton_crear_cuenta = document.getElementById('boton_crear_cuenta')
    boton_crear_cuenta.addEventListener('click', activar_evento_submit_en_crear_cuenta)

    document.querySelectorAll('.input').forEach(input=>{
        input.addEventListener('input', verificar_input )
    })

}

function crear_alerta_de_error_con_id(id, mensaje = '', alertaDiferente = false){
    errorInput = true;
    const errorAnterior = document.querySelector(`#error${id}`)
    
    const element = document.getElementById(id);
    const error = document.createElement('P')
    error.setAttribute('id', `error${id}`)
    error.classList.add('errorInput')
    error.textContent = `**Necesitamos que llenes este campo**`
    if(alertaDiferente){
        error.textContent = `**${mensaje}**`
    }
    element.insertAdjacentElement('afterend', error);
    if(errorAnterior){
        errorAnterior.remove();
    }
}


function verificar_input(e){
    const value = e.target.value
    const errorAnterior = document.querySelector(`#error${e.target.id}`)
    const idInput = e.target.id;
    if(value === ''){
        crear_alerta_de_error_con_id(idInput);
        return
    }
    if(idInput === 'email'){
        const esValido = validarEmail(value)
        if(!esValido){
            crear_alerta_de_error_con_id(idInput, 'Woops! parece que tu email no es valido', true)
            return
        }
    }
   
    if(idInput === 'password'){
        const estaVerificada = verificar_password(idInput);
        if(!estaVerificada) return
    
    }
    if(idInput ==='password_confirmar'){
        const inputPassword = document.getElementById('password')
        if(value !== inputPassword.value){
            crear_alerta_de_error_con_id(idInput, 'Las contraseñas no coinciden', true)
            return
        }
    }
    if(errorAnterior){
        errorAnterior.remove()
    }
    const existeOtroError = document.querySelector('.errorInput')
    if(existeOtroError){
        return
    }
    
    errorInput = false;

}
function verificar_password(idInput){
    const elemento = document.querySelector(`#${idInput}`)
    const minPasswordCaracteres = 6
    const inputPasswordCaracteres = elemento.value.length

    const errorAnterior = document.querySelector(`#error${idInput}`)
    
    if(inputPasswordCaracteres < minPasswordCaracteres){
        
        const error = document.createElement('P')
        error.setAttribute('id', `error${idInput}`)
        error.classList.add('errorInput')
        error.textContent = `**La contraseña debe tener minimo 6 caracteres**`
        elemento.insertAdjacentElement('afterend', error);
        if(errorAnterior){
            errorAnterior.remove();
        }
        return false
    }
    return true

}
    //Termina validacion del front-end

    //validacion con el back-end de los datos..
function crear_cuenta(event){
    event.preventDefault()
    const data = crear_objeto_con_datos_de_formulario(event)
    //Verificar el contenido de los inputs.
    Object.entries(data).forEach(([key, value]) => {
        if(value === ''){
            crear_alerta_de_error_con_id(key)
        }
        
    });
    //Si en el objeto data hay algun campo que este vacio entonces...
    if(errorInput){
        resaltarErrores()
        return
    }
    //crear alerta de cargando.
    crear_alerta_de_cargando();
    enviar_formulario_para_crear_cuenta(data)
    .then(eliminar_alerta_de_cargando)
    .then(validar_respuesta_envio_formulario_crear_cuenta) 
}

function crear_alerta_de_cargando(){
    const modal = crear_modal_en_body()
    insertar_alerta_cargando(modal);
    //inserto alerta en el modal

}
function eliminar_alerta_de_cargando(resultado){
    //eliminar modal que contiene la alerta.
    const modal = document.getElementById('modal');
    modal.remove();
    return resultado
}

function crear_modal_en_body(){
    const modal = document.createElement('DIV');
    const body = document.querySelector('body')
    modal.classList.add('modal', 'modal_crear_cuenta');
    modal.id = 'modal';
    body.appendChild(modal);
    return modal;
}
function insertar_alerta_cargando(modal){
    const alerta = document.createElement('DIV');
    alerta.classList.add('alertaCargando')
    alerta.innerHTML = `<i class="fa-solid fa-spinner fa-2xl"></i>`
    modal.appendChild(alerta);
}

function resaltarErrores(){
   document.querySelectorAll('.errorInput').forEach(error=>{
    error.classList.add('resaltarError');
    setTimeout(() => {
        error.classList.remove('resaltarError')
    }, 500);
   })
}

function validar_respuesta_envio_formulario_crear_cuenta(respuesta){
    if(respuesta.respuesta){
        const div = document.querySelector('#mensaje')
        div.innerHTML = '';
        Swal.fire({
            title: "Buen trabajo!",
            text: `${respuesta.mensaje}`,
            icon: "success",
            fontsize: '5rem',
          }).then(()=>{
            window.location.href = '/login'
          })

       }else{
        const div = document.querySelector('#mensaje')
        div.innerHTML = '';
        respuesta.alertas.error.forEach(mensajeError => {
            const errorParrafo = document.createElement('P');
            errorParrafo.textContent = mensajeError;
            errorParrafo.classList.add('mensaje_de_error')
            div.appendChild(errorParrafo);
        });
        /* div.innerHTML = `<p>Ha fallado la autentificacion</p>` */
       }
}

async function enviar_formulario_para_crear_cuenta(data){

    try {
        const fecha = getFechaActualFormateada();
        const url = '/api/login/crear_cuenta'
        const json = JSON.stringify(data)
        const options = {
            method : 'post',
            body : json
        }
        
       const resultado =  await fetch(url, options);
       const respuesta = await resultado.json();
       //si la respuesta es true mandamos alerta
       return respuesta;
       /* if(respuesta.respuesta){
        Swal.fire({
            title: "Good job!",
            text: `${respuesta.mensaje}`,
            icon: "success"
          });
       }else{
        const div = document.querySelector('#mensaje')
        div.innerHTML = `<p>${respuesta.mensaje}</p>`
       } */
       
        /* .this(resultado=>{
            console.log(resultado.json)
        }) */
        
    } catch (error) {
        console.log(error)
    }
}

function activar_evento_submit_en_crear_cuenta(){
    //Creo evento de submit y se lo aplico al formulario...
    const form = document.getElementById('formulario');
    const event = new Event('submit', {
        'bubbles': true,
        'cancelable': true
    });
    form.dispatchEvent(event);
}

    //Termina validacion del backend

//Termina crear_cuenta



//deshabilitado por el momento..
function contenedorMetodoPagoEvento(){
    document.querySelectorAll('.contenedor_metodo_pago').forEach(contenedor=>{
        contenedor.addEventListener('click', evento=>{
            const metodo = evento.target.dataset.metodoPago;
            metodo_pago = metodo;
            mostrar_informacion_metodo_pago();
        })
    })

}
//crear evento de inputs radio en metodos de pago...
function inputMetodosDePago(){
    const inputs = document.querySelectorAll('.input_radio_metodo_pago');
    inputs.forEach(input=>{
        input.addEventListener('click', event=>{
            const metodo = event.target.dataset.metodoPago;
            metodo_pago = metodo;
            mostrar_informacion_metodo_pago();
        })
    })
}

//Muestra informacion dependiendo del metodo seleccionado por el usuario
function mostrar_informacion_metodo_pago(){
    const div = document.createElement('DIV')
    div.classList.add('contenedor_crear_pedido')
    //Elimina el contenedor anterior..
    const contenedorAnterior = document.querySelector('.contenedor_crear_pedido');
    if(contenedorAnterior){
        contenedorAnterior.remove()
    }
    //Muestra html dependiedno del metodo de pago.
    if(metodo_pago === 'transferencia'){
        const contenedor = document.querySelector("[data-metodo-pago='transferencia'")
        div.innerHTML = `<button class="boton_crear_pedido boton">Terminar pedido</button>
            <p class="crear_pedido_descripcion">Listo... Selecciona crear pedido para
                crear la orden, una vez tengas la orden realizada, solo falta realizar
                el pago y empezaremos a realizar y despachar tu pedido... 😎
            </p>`
            /* console.log(contenedor) */
            contenedor.appendChild(div);
            metodo_pago = 'transferencia';
            document.querySelector('.boton_crear_pedido').addEventListener('click', crear_pedido)
            return
    }
    if(metodo_pago === 'pse'){
        const contenedor = document.querySelector("[data-metodo-pago='pse'")
        div.innerHTML = `<p>Todavia no hemos habilitado transferencia PSE... esperalo pronto :D</p>`
        metodo_pago = 'pse';
        contenedor.appendChild(div);
        return
    }

}

function obtenerValueElementById(element){
    const elementHtml = document.getElementById(element)
    const value = elementHtml.value
    return value;
}
//Envio definitivo del pedido, para crear el pedido completo en la base de datos..
async function crear_pedido(){

    try {
        const fecha = getFechaActualFormateada();
        //Crear objeto que sera convertido a JSON y enviado por fetch POST
        const datos = {
            carritoDeCompra : carritoDeCompra.productos,
            /* organizo para la DB */
            pedidos : {
                fecha : fecha,
                direccion : formularioDatos.direccion,
                departamento : formularioDatos.departamento,
                ciudad : formularioDatos.ciudad,
                metodo_pago : metodo_pago,
                informacion_adicional : formularioDatos.informacion_extra,
                
            },
            usuario: {
                nombre : formularioDatos.nombre,
                apellido : formularioDatos.apellido,
                email : formularioDatos.email,
                telefono : formularioDatos.telefono,
                cedula: formularioDatos.cedula
            }
            
        }
        
        const url = '/api/crear_orden';
        jsonData = JSON.stringify(datos);

        const options = {
            method : 'POST',
            body : jsonData
        }
        console.log(datos);
        //Envio del objeto a la DB
        crear_alerta_de_cargando();
        const respuesta = await fetch(url, options)
        const resultado = await respuesta.json();
        eliminar_alerta_de_cargando();
        //Alerta si el resultado es exitoso.
        if(resultado.exito){
            carritoDeCompra.productos = resultado.carrito
            Swal.fire({
                icon: "success",
                title: "Pedido creado",
                text: "El pedido ha sido guardado con exito",
                button: 'OK'
              }).then(() =>{
                //crear un div con insutrcciones para el usuario... 
                mostrar_instrucciones_despues_de_pago(resultado);  
              })
        }

    } catch (error) {
        console.log(error)
        Swal.fire({
            icon: "error",
            title: "Ha habido un error",
            text: "El pedido no se ha podido crear.."
          });
    }
}
//Crea instrucciones en el paso-3
function mostrar_instrucciones_despues_de_pago(resultado){
    const div = document.querySelector('main')
    //crear div
    div.innerHTML = `
    <h1>Gracias por tu compra!</h1>
    <div class="div_instrucciones_pago contenedor">
    <h2>Instrucciones a seguir</h2>
    <div class="contenedorInstrucciones">
        <p>Hemos enviado un correo electronico con las siguientes instrucciones, porfavor revisar la bandeja de spam: </p>
        <p>Consigna a nuestra cuenta bancolombia #: <span>412514</span></p>
        <p>Manda un screenshot a nuestro wp #124123123312 con el pedido # <span>${resultado.pedidoId}</span></p>
        <p>Recibe tus productos en la puerta de tu casa...!</p>
    </div>
    <div class="resumenCompra"></div>
    <h2>Resumen de tu pedido</h2>
    <p>El pedido quedo a nombre de : <span> ${resultado.nombre} </span>  </p>
    <p>El costo total a pagar es de : <span> $${parseInt(resultado.costo_total).toLocaleString()} </span></p>
    <ul class="lista-productos" id="lista-productos">
        <p>Los productos que te mandaremos son:</p>
    </ul>
    <p>La direccion a la que te mandaremos los productos es: <span>${resultado.direccion}</span> </p>
    </div>
    `
    const lista_productos = document.querySelector('#lista-productos');
    resultado.productos.forEach(producto=>{
        const productoDiv = document.createElement('LI');
        productoDiv.innerHTML = `<p><span>${producto.nombre}</span> x${producto.cantidad}</p>`
        lista_productos.appendChild(productoDiv);
    })
}

function formulario_evento(){
    const formulario = document.getElementById('formulario_carrito_de_compras');
    formulario.addEventListener('submit',  event=>{

        event.preventDefault()
        //guardo del formulario en un objeto...
        const data = crear_objeto_con_datos_de_formulario(event)
        validar_formulario_informacion_para_pedido(data)
        .then(resultado =>{
             //Si el resultado es falso significa que no paso la validacion y cortamos el codigo
            if(!resultado){
                validado = false;
                return
            }
            validado = true;
            formularioDatos = data;
            cambiarSeccionAdelante();
        }) 
    })
}

function crear_objeto_con_datos_de_formulario(event){
    const data = Object.fromEntries(new FormData(event.target))
    return data;
}


//Validar formulario de informacion para el pedido
async function validar_formulario_informacion_para_pedido(data){
    try {
        jsonData = JSON.stringify(data);

        const url = '/api/validar_formulario';
        const options = {
            method : 'POST',
            body : jsonData
        }
        console.log(data);
        const resultado = await fetch(url, options)
        const respuesta = await resultado.json();
        //si hay un error creamos alertas.
        if(respuesta.error){
            //eliminamos alertas anteriores
            const divAlerta = document.querySelector('#alertas')
            while(divAlerta.firstChild){
                divAlerta.removeChild(divAlerta.firstChild);
            }
            respuesta.error.forEach(error=>{
                const parrafo = document.createElement('P')
                parrafo.classList.add('alerta', 'error')
                parrafo.textContent = error
                divAlerta.appendChild(parrafo)
                return false;
            })
            setTimeout(() => {
                while(divAlerta.firstChild){
                    divAlerta.removeChild(divAlerta.firstChild);
                }
            }, 5000);
            //scrollear al usuario hasata la primera alerta
            const alerta = document.querySelector('.alerta');
            alerta.scrollIntoView({ behavior: 'smooth' });
        }else{
            return true;
        }

    } catch (error) {
        const divAlerta = document.querySelector('#alertas')
        const parrafo = document.createElement('P')
        parrafo.textContent = error
        divAlerta.appendChild(parrafo)
    }
}
//Evento botones Tabs:
function tabs(){
    let botones = document.querySelectorAll('.tabs button')
    botones.forEach( (boton) => {
        boton.addEventListener('click', function(e){
            if(e.target.dataset.paso === '3'){
                const resultado = activar_evento_submit_en_formulario_carrito_compras()
                if(!resultado){
                    return
                }
            }
           
            paso = parseInt(e.target.dataset.paso);
           
            mostrarSeccion();
            /* botonesPaginador(); */
            /* boton.classList.toggle('actual') */
        })
    })

}
//Mostrar seccion igual al paso:
function mostrarSeccion(){

    //quitar mostrar a seccion anterior
    let seccionAnterior = document.querySelector('.actual');
    const botonAnterior = document.querySelector('#anterior');
    const botonSiguiente = document.querySelector('#siguiente');


    /* let botonAnterior = document.querySelector('.actual'); */
    if(paso === 3 ){
        const divAlerta = document.querySelector('#alertas')
        while(divAlerta.firstChild){
            divAlerta.removeChild(divAlerta.firstChild);
        }
        //validar los campos del usuario...
        
        if(!validado){
            const alertasDiv = document.getElementById('alertas')
            alertasDiv.innerHTML = `<p class='alerta error'> Faltan datos por rellenar</p>`
            return
        }
        console.log('el usuario esta validado, pasando a la seccion 3')
        botonSiguiente.classList.add('hidden')
        /* mostrarResumen(); */
    }
    
    if(seccionAnterior){
        seccionAnterior.classList.remove('actual');
        seccionAnterior.classList.add('ocultar')
        
        const botonAnterior = document.querySelector('.hidden')
        if(botonAnterior){
            botonAnterior.classList.remove('ocultar')

        }
        /* botonAnterior.classList.remove('actual'); */
    }
    if(paso === 1){
        botonAnterior.classList.add('hidden')
    }
    if(paso === 2){
        botonAnterior.classList.remove('hidden')
        botonSiguiente.classList.remove('hidden')
        /* crearBotonAnterior();
        crearBotonSiguiente(); */
    }
    //Agrega mostrar a seccion seleccionada.
    let seccion = document.querySelector(`#paso-${paso}`)
    seccion.classList.add('actual');
    seccion.classList.remove('ocultar')

    //Resalto seccion actual.
    /* boton = document.querySelector(`[data-paso='${paso}']`);
    boton.classList.add('actual'); */
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
            option.dataset.departamentoId = departamento.id
            select.appendChild(option);
        });
    })

    select.addEventListener('change', eventoSelect)
}
function eventoSelect(event){
    const idDepartamento = event.target.options[event.target.selectedIndex].attributes['data-departamento-id'].value;
    get_ciudades_de_un_departamento(idDepartamento)
    .then(ciudades=>{
        crear_options_de_ciudad(ciudades)
        console.log(ciudades)
    })

}

function crear_options_de_ciudad(ciudades){
    const select = document.querySelector('#ciudad')
    ciudades.forEach(ciudad => {
        const option = document.createElement('OPTION');
        option.textContent = ciudad.name
        option.value = ciudad.name
        option.dataset.ciudadId = ciudad.id
        select.appendChild(option);
    });
    select.removeAttribute('disabled');
}

async function get_ciudades_de_un_departamento(id_departamento){
    try {
        const url = `https://api-colombia.com/api/v1/Department/${id_departamento}/cities`
        const resultado = await fetch(url);
        const json = await resultado.json()
        return json;
    } catch (error) {
        console.log(error);
    }
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

//Recuperar carrito de compras...
async function recuperarCarritoCompras (){
    try {
        const url = '/api/cuadros/guardar_carrito_compras';
        //Envio de peticion a la DB
        const respuesta = await fetch(url)
        const resultado = await respuesta.json();
        carritoDeCompra=resultado.carritoDeCompra
        montoTotal = resultado.montoTotal;
        mostrarCarritoDeCompras();

    } catch (error) {
        console.log(error);
    }
}

function mostrarCarritoDeCompras(){
    guardar_carrito_compras_en_session();
    const interfaz = document.querySelector('.contenedorProductosFormulario')
    const contenedorBig = document.querySelector('#tbody')
        //borras todos todos los producots y los vuelvo a crear.... (actualizar)
        while(contenedorBig.firstChild){
            contenedorBig.removeChild(contenedorBig.firstChild);
            if(interfaz.firstChild){
                interfaz.removeChild(interfaz.firstChild)

            }
        }
        //si el carrito no tiene productos ..
        if(carritoDeCompra.productos.length === 0){
            const parrafo = document.createElement('P');
            parrafo.classList.add('parrafoCarritoVacio');
            parrafo.innerHTML = `El carrito esta vacio ... <br /> Lo sentimos selecciona algo para poder seguir con alguna compra ...  😭`
            interfaz.appendChild(parrafo);
            return
        }
        //Crear productos e insertar en interfaz..
        const tbody = document.getElementById('tbody');
        carritoDeCompra.productos.forEach(producto =>{
            const {id, nombre, precio, descripcion, size, disponible, imagen, cantidad} = producto;

            const productoDiv = document.createElement('DIV');
            productoDiv.classList.add("interfazCampo");

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = nombre;
            nombreProducto.classList.add('nombre-producto')

            const precioProducto = document.createElement('P');
            precioProducto.textContent = `$${parseInt(precio).toLocaleString()}`;
            
            precioProducto.classList.add('precio-producto')

            const cantidadProducto = document.createElement('P');
            cantidadProducto.textContent = `Cantidad: ${cantidad}`;
            cantidadProducto.classList.add('cantidad_producto');

            const botonMas = document.createElement('BUTTON');
            botonMas.classList.add('botonChico');
            botonMas.textContent = '+';
            botonMas.addEventListener('click', function(){
                agregarCantidad(producto, true);
            })

            const botonMenos = document.createElement('BUTTON');
            botonMenos.classList.add('botonChicoRojo');
            botonMenos.textContent = '-';
            botonMenos.addEventListener('click', function(){
                restarCantidad(producto, true);
            })


            const sizeProducto = document.createElement('P');
            sizeProducto.innerHTML = `<span>Tamaño: </span> ${size}`;

            const descripcionProducto = document.createElement('P');
            descripcionProducto.classList.add('descripcion-producto')
            descripcionProducto.textContent = descripcion;

            const iconoDiv = document.createElement('DIV');
            iconoDiv.classList.add('iconoDiv');

            const icono = document.createElement('I');
            icono.classList.add('fa-solid', 'fa-trash');
            icono.dataset.idProducto = id;
            icono.addEventListener('click', function(e){
                const id_producto_a_eliminar = e.target.dataset.idProducto;
                eliminarProducto(id_producto_a_eliminar)
            })

            iconoDiv.appendChild(icono)
            
            const divInfo = document.createElement('DIV');
            divInfo.classList.add('infoProductoCarrito');
            divInfo.appendChild(nombreProducto)
            divInfo.appendChild(sizeProducto)
            divInfo.appendChild(descripcionProducto)

            const divBotonesCantidad = document.createElement('DIV');
            divBotonesCantidad.classList.add('cantidad_producto_div')
            divBotonesCantidad.appendChild(botonMas)
            divBotonesCantidad.appendChild(botonMenos)
            divBotonesCantidad.appendChild(iconoDiv);

            const imagenProducto = document.createElement('IMG');
            imagenProducto.src = `/img/productos/${imagen}`;


            const precioTotal = precio * cantidad;
            const precioTotalParrafo = document.createElement('P')
            precioTotalParrafo.textContent = `$${precioTotal.toLocaleString()}`
            precioTotalParrafo.classList.add('precio-total-produto')
           
            //Creo row de tabla e ingreso los datos a la fila...
            const traw = document.createElement('TR');
            const tdDetalles = document.createElement('TD');
            tdDetalles.classList.add('tabla_DetallesDelProducto')
            tdDetalles.appendChild(imagenProducto)
            tdDetalles.appendChild(divInfo);

            const tdCantidad = document.createElement('TD');
            tdCantidad.appendChild(cantidadProducto);
            tdCantidad.appendChild(divBotonesCantidad);
            

            const tdPrecio = document.createElement('TD');
            tdPrecio.appendChild(precioProducto)

            const tdTotal = document.createElement('TD');
            tdTotal.appendChild(precioTotalParrafo);
            
            traw.appendChild(tdDetalles)
            traw.appendChild(tdCantidad)
            traw.appendChild(tdPrecio)
            traw.appendChild(tdTotal)
            tbody.appendChild(traw);
            
        })
        //Muestra el montoTotal en carrito de compras.
        const parrafoMontoTotal = document.createElement('P');
        parrafoMontoTotal.classList.add('parrafoMontoTotal')
        /* montoTotal = sumarProductosCarrito(); */
        parrafoMontoTotal.innerHTML = `<span>Monto Total:</span> $${carritoDeCompra.montoTotal().toLocaleString()} COP`;
        interfaz.appendChild(parrafoMontoTotal)
        //Crea boton para seguir a la siguiente seccion...
    

       /*  const botonSiguiente = document.createElement('BUTTON');
        botonSiguiente.classList.add('botonAzul');
        botonSiguiente.setAttribute('id', 'botonSiguiente')
        botonSiguiente.textContent = 'Siguiente..'
        botonSiguiente.onclick = cambiarSeccionAdelante;

        const botonSiguienteDiv = document.createElement('DIV');
        botonSiguienteDiv.classList.add('botonSiguienteDiv')
        botonSiguienteDiv.appendChild(botonSiguiente); */

        /* interfaz.appendChild(botonSiguienteDiv) */


}
//evento botones}
function agregar_evento_botones_paginacion(){
    anterior = document.querySelector('#anterior')
    siguiente = document.querySelector('#siguiente')

    siguiente.addEventListener('click', cambiarSeccionAdelante)
    anterior.addEventListener('click', cambiarSeccionAtras)

}

//Activa el evento submit en el formulario
function activar_evento_submit_en_formulario_carrito_compras(){
    //Creo evento de submit y se lo aplico al formulario...
    const form = document.getElementById('formulario_carrito_de_compras');
    const event = new Event('submit', {
        'bubbles': true,
        'cancelable': true
    });
    form.dispatchEvent(event);
}

//Cambiar seccion
function cambiarSeccionAdelante(e){
    const ultimoPaso = 3;
    if(paso === 2 ){
        activar_evento_submit_en_formulario_carrito_compras();
        if(!validado){
            return
        }
    }

    /* if(paso === 2 && validado === false){
        const alertasDiv = document.getElementById('alertas')
        alertasDiv.innerHTML = `<p class='alerta error'> Faltan datos por rellenar</p>`
        return
    } */

    if(paso === ultimoPaso){
        return
    }
    paso++;
    mostrarSeccion();
}
function cambiarSeccionAtras(){
    const primerPaso = 1;
    if(paso === primerPaso){
        return
    }
    paso--;
    mostrarSeccion();
}

//Eliminar producto id
function eliminarProducto(id){

    carritoDeCompra.productos.forEach(producto =>{
        if(producto.id === id){
            const index_producto = carritoDeCompra.productos.indexOf(producto.id);
            carritoDeCompra.productos.splice(index_producto, 1);
            console.log(carritoDeCompra);
            mostrarCarritoDeCompras();
        }
    })
    guardar_carrito_compras_en_session();

}
async function guardar_carrito_compras_en_session(){
    /* const datos = new FormData();
    datos.append('carrito', carritoDeCompra) */
    const url = '/api/session/guardar_carrito';
    options = {
        method: 'post',
        body: JSON.stringify(carritoDeCompra.productos)
    }
    try {
        const resultado = await fetch(url, options);
        const respuesta = resultado.json();
        return respuesta;
        
    } catch (error) {
        console.log(error)
    }

}
//Enviar peticion para guardar el carrito con la sesion del usuario....
async function guardar_carrito_en_session (){
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
            monto_total : montoTotal,
            metodo_pago : metodo_pago
        }
        const url = '/api/cuadros/guardar_carrito_compras';
        jsonData = JSON.stringify(datos);

        const options = {
            method : 'POST',
            body : jsonData
        }
        console.log(datos)
        //Envio del objeto a la DB
        const respuesta = await fetch(url, options)
        const resultado = await respuesta.json();
        console.log(resultado);
        //Alerta si el resultado es exitoso.
        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Pedido creado correctamente",
                text: "El pedido ah sido creado crrectamente, a continuacion lee lo siguiente",
                button: 'OK'
              }).then(() =>{
                window.location.href = '/carrito_de_compras';       
              })
        }

    } catch (error) {
        console.log(error);
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
const formattedDate = `${year}-${month}-${day}`;
return formattedDate;
}
//Cuadros //
//Mostrar Resultados ....
//Consultar la api
async function get_cuadros(){
    try {
        const url = '/api/cuadros/getcuadros?n=6';
        const resultado = await fetch(url);
        const json = await resultado.json()
        imprimir_cuadros(json);
    } catch (error) {
        console.log(error);
    }

}
function imprimir_cuadros(json){

    json.forEach( cuadro =>{
        const { descripcion, disponible, id, imagen, nombre, precio, size} = cuadro
        
        const section = document.getElementById('cuadros');
        
        const cuadroDiv = document.createElement('DIV');
        cuadroDiv.classList.add('cuadro')
        cuadroDiv.onclick = function(){
            window.location.href = `/producto?id=${id}`
        }

        const imagenDiv = document.createElement('DIV')
        imagenDiv.classList.add('imagenCuadroDiv');
        imagenDiv.style.backgroundImage = `url(/img/productos/${imagen})`
        

        const infoDiv = document.createElement('DIV');
        infoDiv.classList.add('cuadro-info');
        infoDiv.dataset.idCuadro = id;

        const nombreCuadro = document.createElement('P')
        nombreCuadro.classList.add('nombre-cuadro');
        nombreCuadro.textContent = nombre;

        const precioCuadro = document.createElement('P')
        precioCuadro.classList.add('precio-cuadro');
        precioCuadro.textContent =  `$${parseFloat(precio).toLocaleString()}`
        console.log();

        infoDiv.appendChild(nombreCuadro);
        infoDiv.appendChild(precioCuadro);
        /* crearBotonComprarActual(infoDiv, cuadro); */

        // imagenDiv.appendChild(infoDiv);

        cuadroDiv.appendChild(imagenDiv);
        cuadroDiv.appendChild(infoDiv);

        section.appendChild(cuadroDiv);

    })
}
//mostrar Producto con modal...
function mostrar_cuadro(cuadro){
    const modal = crear_modal();
    const img = document.createElement('IMG');
    img.href = `/img/productos/${cuadro.imagen}`;

    console.log(cuadro);
    
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
//Funcion que agregar el cuadro al carrito..
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
    //descomentarlo cuando agregue la funcion para los usuarios
    /* userBoton.addEventListener('click', mostrarInterfazCarritoCompras); */

    
}
function mostrarInterfazCarritoCompras(){
    actualizarInterfazCarritoCompra();

    const modal = document.querySelector('.modalCarrito');
    const interfazCarritoCompras = document.querySelector('#carritoDeCompras')
    modal.appendChild(interfazCarritoCompras);
    esconderScroll();
}
function actualizarInterfazCarritoCompra(){
    //guardar carrito de compras en la session.
    guardar_carrito_compras_en_session();
    //Si ya existe un modal, lo elimina..
    const modalAnterior = document.querySelector('.modalCarrito');
    if(modalAnterior){
        modalAnterior.remove();
    }
    crearModal();

    const interfaz = document.querySelector('.contenedorCarritoCompras')
    //si el carrito no tiene productos ..
    if(carritoDeCompra.productos.length < 1){
        
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
        
        carritoDeCompra.productos.forEach(producto =>{
            const {id, nombre, precio, descripcion, size, disponible, imagen, cantidad} = producto;
            
            const productoDiv = document.createElement('DIV');
            productoDiv.classList.add("interfazCampo");

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = nombre;
            nombreProducto.classList.add('nombre-producto')
            const precioProducto = document.createElement('P');
            precioProducto.textContent = `$${parseInt(precio).toLocaleString()}`;
            precioProducto.classList.add('precio-producto')
            const cantidadProducto = document.createElement('P');
            cantidadProducto.textContent = `Cantidad: ${cantidad}`;

            const botonMas = document.createElement('BUTTON');
            botonMas.classList.add('botonChico');
            botonMas.textContent = '+';
            botonMas.onclick = function(){
                agregarCantidad(producto)
            };
            /* botonMas.addEventListener('click', function(){
                agregarCantidad(producto);
            }) */

            const botonMenos = document.createElement('BUTTON');
            botonMenos.classList.add('botonChicoRojo');
            botonMenos.textContent = '-';
            botonMenos.addEventListener('click', function(){
                restarCantidad(producto);
                actualizarInterfazCarritoCompra();
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
            imagenProducto.src = `/img/productos/${imagen}`;

            productoDiv.appendChild(divInfo);
            productoDiv.appendChild(imagenProducto);
            interfaz.appendChild(productoDiv);
            
        })
        //Muestra el montoTotal en carrito de compras.
        const parrafoMontoTotal = document.createElement('P');
        parrafoMontoTotal.classList.add('parrafoMontoTotal')
        /* montoTotal = sumarProductosCarrito(); */
        parrafoMontoTotal.textContent = `Monto total: $${carritoDeCompra.montoTotal().toLocaleString()}`;
        interfaz.appendChild(parrafoMontoTotal)

        //Boton de comprar en interfaz de carrito de compras.
        const botonComprar = document.createElement('BUTTON');
        botonComprar.classList.add('boton')
        botonComprar.textContent = `Comprar`
        botonComprar.addEventListener('click', comprar_carrito )
        interfaz.appendChild(botonComprar);
}
function comprar_carrito(){
    guardar_carrito_compras_en_session()
    .then(window.location.href = '/carrito_de_compras')
}
function sumarProductosCarrito(){
    let total = 0
    carritoDeCompra.forEach(producto => {
        let precio = parseInt(producto.precio) * producto.cantidad
        total = total + precio;
    });
    return total;
}

function actualizar_resumen_de_carrito_compras(){

}

function agregarCantidad(producto, compras = false){
    const index = carritoDeCompra.productos.indexOf(producto);
    carritoDeCompra.productos[index].cantidad ++;
    if(compras){
        console.log(carritoDeCompra.productos)
        mostrarCarritoDeCompras()  
        return
    }
    actualizarInterfazCarritoCompra();
}
//Si compra es true, significa que se usara carrito_de_compras
function restarCantidad(producto, compras = false){
    const index = carritoDeCompra.productos.indexOf(producto);
    carritoDeCompra.productos[index].cantidad --;
    if(carritoDeCompra.productos[index].cantidad < 1){
        borrarProductoDeCarritoDeCompra(producto.id, producto);
    }
    if(compras){
        mostrarCarritoDeCompras();
        return
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
        carritoDeCompra.productos.forEach( producto =>{
            if(producto.id === id){
            const index = carritoDeCompra.productos.indexOf(producto);
            carritoDeCompra.productos.splice(index, 1)
            //Formateo el objeto a por default... para que al agregarlo al carrito no tenga problemas..
            delete objetoProducto.cantidad;
            actualizarInterfazCarritoCompra();
            }
        })
    })

}

function borrarProductoDeCarritoDeCompra(id, objetoProducto){
    carritoDeCompra.productos.forEach( producto =>{
        if(producto.id === id){
        const index = carritoDeCompra.productos.indexOf(producto);
        carritoDeCompra.productos.splice(index, 1)
        //Formateo el objeto a por default... para que al agregarlo al carrito no tenga problemas..
        /* delete objetoProducto.cantidad; */
        

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

//empieza formulario index

function evento_formulario_index(){
    const form = document.querySelector('.formulario');
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const datos = crear_objeto_con_datos_de_formulario(e);
        //enviar datos
        enviar_formulario_para_cuadro_personalizado(datos)

    })
}
async function enviar_formulario_para_cuadro_personalizado(datos){
    const data = new FormData();
    data.append('nombre',datos.nombre);
    data.append('apellido',datos.apellido);
    data.append('contacto',datos.contacto);
    data.append('telefono',datos.telefono);
    data.append('email',datos.email);
    data.append('mensaje',datos.mensaje);

    /* console.log([...data]); */
    const url = '/api/formulario_cuadro_personalizado';
    const options = {
        method: 'POST',
        body: data
    }
    try {
        crear_alerta_de_cargando();
        const resultado = await fetch(url,options);
        const respuesta = await resultado.json();
        eliminar_alerta_de_cargando();
        if(respuesta.tipo === 'exito'){
            mostrar_alerta('exito',respuesta.mensaje,'.informacionPersonal legend');
            document.querySelector('[type="submit"]').remove();
        }else{
            respuesta.alertas.error.forEach(alerta=>{
                mostrar_alerta('error',alerta,'.informacionPersonal legend')
            })
        }
        console.log(respuesta);
    } catch (error) {
        error
    }
}

function mostrar_alerta(tipo,mensaje,referencia){
    const alerta = document.createElement('DIV');
    alerta.classList.add('alerta',tipo);
    alerta.textContent = mensaje
    const divRef = document.querySelector(referencia);
    divRef.parentElement.insertBefore(alerta, divRef )

    setTimeout(() => {
        alerta.remove();
    }, 4000);

}
//termina formulario index

//Agrega evento al menuHamburgesa
function eventoMenu(){
    menu = document.querySelector('.hamburgerMenu');
    menu.addEventListener('click', function(){
        mostrarMenu();
        
    })
}
function mostrarMenu(){
    ocultarHeader();
    const modal = crear_modal();
    esconderScroll();
    modal.innerHTML = `
    <div class="menu">
            <div class="menuHeader">
                <i id="cerrar_menu" class="fa-solid fa-x fa-2xl icono"></i>
                <div class="menuCampo">
                    <a href="/">Home</a>
                </div>
                <div class="menuCampo">
                    <a href="/productos">Todos los Productos</a>
                </div>
                <div class="menuCampo">
                    <a href="/carrito_de_compras">Carrito de compra</a>
                </div>
                
            </div>
            <div class="menuFooter">
                <div class="redes contenedor">
                    <a href="https://www.facebook.com/profile.php?id=61559826170800"><i class="fa-brands fa-square-facebook fa-2xl"></i></i></a>    
                    <a href="https://www.instagram.com/cyberr.art/"><i class="fa-brands fa-square-instagram fa-2xl"></i></a>    
                    <a href="https://www.tiktok.com/@cyberartcraft"><i class="fa-brands fa-tiktok fa-2xl"></i></a>    
                </div>
            </div>
        </div>
    `
    modal.addEventListener('mousedown', function(event){
        evento_eliminar_modal(event, '.menu')
    })
    botonCerrar = document.querySelector('#cerrar_menu')
    botonCerrar.addEventListener('click', eliminar_modal_and_mostrar_header)
    return;
}
//Function para crear modal
function crear_modal(){
    const body = document.querySelector('body');
    const modal = document.createElement('DIV');
    modal.classList.add('modal');
    modal.innerHTML = `
    <div class="modal_contenido">

    </div>
    `
    body.appendChild(modal);
    const modal_contenido = document.querySelector('.modal_contenido')
    return modal_contenido;


}
function evento_eliminar_modal(event, targetClosest){
    eventClass = event.target.closest(targetClosest)
    if(!eventClass){
        eliminar_modal();
        mostrarHeader();
        mostrarScroll();
    }
}
//function para eliminar el modal
function eliminar_modal(){
    const modal = document.querySelector('.modal');
    modal.remove();
    mostrarHeader();
}
//Oculta el modal y header presionando afuera del menu...
function eliminar_modal_and_mostrar_header(){
    eliminar_modal();
    mostrarHeader();
    mostrarScroll();
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
    const header = document.querySelector('.header');
    const sectionReferencia = document.querySelector('.nombre-pagina');
    const main = document.querySelector('main');
    if(!sectionReferencia) return;

    document.addEventListener('scroll', function(){
        let scrollNumber = sectionReferencia.getBoundingClientRect().top
        if(scrollNumber < 1){
            header.classList.add('positionFixed');
            main.classList.add('padding-for-fixedHeader');
        }
        else{
            header.classList.remove('positionFixed');
            main.classList.remove('padding-for-fixedHeader');
        }
    })
}
//Agrega evento a inputs Radio.
function eventoInputs(){
    inputBoton = document.querySelectorAll("[name='contacto']")
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
        <input id="telefono" type="tel" placeholder="Tu numero de celular" name="telefono">
        `
    }
    if(id === 'byEmail'){
        div.innerHTML = `
        <label for="email">Correo</label>
        <input id="email" type="email" placeholder="Tu correo electronico" name="email">
        `
    }

}