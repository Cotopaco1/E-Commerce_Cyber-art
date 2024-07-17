import{activar_evento_submit_by_id,capturar_y_verificar_datos_de_formulario,crear_alerta_de_cargando,eliminar_alerta_de_cargando}from"./helper.js";document.addEventListener("DOMContentLoaded",(function(){iniciarApp()}));let productos_json={},pedidos_json={};function iniciarApp(){mostrarDatosResumen(),eventosBotones(),eventos_botones_menu()}function eventos_botones_menu(){document.querySelector("#home").addEventListener("click",actualizarPagina),document.querySelector("#productos").addEventListener("click",mostrarProductos),document.querySelector("#nuevo_producto").addEventListener("click",mostrar_crear_producto),document.querySelector("#pedidos").addEventListener("click",mostrarPedidos)}function eventosBotones(){document.querySelector("#boton_pedidos").addEventListener("click",mostrarPedidos),document.querySelector("#boton_productos").addEventListener("click",mostrarProductos)}function mostrarPedidos(){cambiarTitulo("Pedidos"),crearTabla(["id","fecha","estado","monto_total","actions"]),solicitud_get_por_url("/api/admin/get_pedidos").then(llenarAndMostrarTablaPedidos),insertar_buscador()}function insertar_buscador(){const e=document.querySelector(".contenido"),n=document.createElement("DIV");n.classList.add("div_buscador"),n.innerHTML='\n    <label htmlFor="buscador">Filtrar por fecha    <input class="input" type="date" id="buscador" /></label>\n    ',e.prepend(n);document.getElementById("buscador").addEventListener("input",evento_buscador)}function evento_buscador(e){const n=e.target.value;document.getElementById("tbody").innerHTML="",solicitud_get_por_url(`/api/admin/get_pedidos_where?fecha=${n}`).then((e=>{llenarAndMostrarTablaPedidos(e)}))}function llenarAndMostrarTablaPedidos(e){const n=document.getElementById("tbody");pedidos_json=e.respuesta;let o=0;e.respuesta.forEach((e=>{const{id:t,fecha:a,status:r,total:i}=e;if(t===o)return;const d=document.createElement("TR");d.innerHTML=`\n        <td><p>${t}</p></td>\n        <td><p>${a}</p></td>\n        <td><p>${r}</p></td>\n        <td><p>$<span>${parseInt(i).toLocaleString()}</span></p></td>\n        <td>\n        <button data-id="${t}" class="boton-ver">Ver</button>\n        </td>\n        `,n.appendChild(d),o=t})),evento_botones_pedidos_action()}function evento_botones_pedidos_action(){document.querySelectorAll(".boton-ver").forEach((e=>{e.addEventListener("click",mostrar_ver_pedido)}))}function mostrar_ver_pedido(e){crearModal();const n=e.target.dataset.id;insertar_al_modal_pedido_ver(pedidos_json.find((function(e){return e.id===n})))}function insertar_al_modal_pedido_ver(e){const{id:n,fecha:o,status:t,nombre:a,email:r,telefono:i,direccion:d,informacion_adicional:c,total:s,metodo_pago:l,cedula:u}=e;document.querySelector(".contenido_modal").innerHTML=`\n    <div class="producto_div pedido_div">\n            <div class="pedido_div_uno">\n                <p>ID Pedido: <span>${n}</span></p>\n                <p>Fecha: <span>${o}</span></p>\n                <p>Estado: <span>${t}</span></p>\n        \n            </div>\n            <div class="pedido_div_dos">\n                <p>Nombre: <span>${a}</span></p>\n                <p>Correo: <span>${r}</span></p>\n                <p>telefono: <span>${i}</span></p>\n                <p>Cedula: <span>${u}</span></p>\n            </div>\n            <div class="div_pedido_tres">\n                <p>Direccion: <span>${d}</span></p>\n            </div>\n            <div> <p>Informacion Adicional: <span>${c}</span></p></div>\n            <div class="div_productos_comprados">\n\n            </div>\n            <div class="div_pedido_footer">\n                <p>Estado: <span>${t}</span></p>\n                <p>Metodo de pago: <span>${l}</span></p>\n                </div>\n            <p>Total: <span>$${parseInt(s).toLocaleString()}</span></p>\n\n        <div class="producto_div_footer">\n            <button class="boton-eliminar" id="cancelar_boton">Volver</button>\n        </div>\n    </div>\n    `;document.getElementById("cancelar_boton").addEventListener("click",eliminarModal)}function mostrar_crear_producto(){insertar_formulario_crear(),cambiarTitulo("Crear Producto"),document.querySelector(".formulario").addEventListener("submit",evento_formulario_crear)}async function evento_formulario_crear(e){e.preventDefault();const n=new FormData(e.target);try{const e=`${location.origin}/api/admin/crear_producto`,o={method:"post",body:n};crear_alerta_de_cargando();const t=await fetch(e,o),a=await t.json();if(eliminar_alerta_de_cargando(),a.resultado)Swal.fire({title:"Buen trabajo!",text:`${a.mensaje}`,icon:"success",fontsize:"5rem"}).then(mostrarProductos);else{console.log(a);const e=document.getElementById("mensaje_error");e.innerHTML="",a.alertas.forEach((n=>{const o=document.createElement("P");o.textContent=n,e.appendChild(o)}))}}catch(e){console.log(e)}}function insertar_formulario_crear(){document.querySelector(".contenido").innerHTML='\n     <form class="producto_div formulario crear_producto" id="formulario" enctype="multipart/form-data">\n                <div class="informacion_producto ">\n                    <label htmlFor="nombre_editar">Nombre: <input name="nombre" id="nombre_editar" type="text" /></label>\n                    <label >Precio: <input name="precio" id="precio_editar" type="text"/></label>\n                    <label htmlFor="">\n                    Disponible:\n                    <select id="select_disponible" name="disponible">\n                        <option value="1">Si</option>\n                        <option value="0">No</option>\n                    </select>\n                    </label>\n                    <label htmlFor="">Tamaño: <input type="text" name="size" /></label>\n                </div>\n           \n            <div class="div_producto_dos">\n                <input type="file" name="imagen" />\n                 <label for="descripcion" >Descripcion:</label>\n                  <textarea name="descripcion" id="descripcion" placeholder="Escribe la descripcion" ></textarea>\n            </div>\n        <div id="mensaje_error"></div>\n        <div class="producto_div_footer">\n            <input type="submit" value="Crear producto" id="crear_producto" class="boton-ver" />\n            \n        </div>\n    </form>\n    '}function evento_botones_action(){document.querySelectorAll(".boton-editar").forEach((e=>{e.addEventListener("click",mostrar_editar_producto)})),document.querySelectorAll(".boton-ver").forEach((e=>{e.addEventListener("click",mostrar_ver_producto)})),document.querySelectorAll(".boton-eliminar").forEach((e=>{e.addEventListener("click",eliminar_producto)}))}async function eliminar_producto(e){const n={id:e.target.dataset.id};try{const e={method:"POST",body:JSON.stringify(n)},o=await fetch("/api/admin/eliminar_producto",e),t=await o.json();t.resultado?Swal.fire({title:"Buen trabajo!",text:`${t.mensaje}`,icon:"success",fontsize:"5rem"}).then(mostrarProductos):Swal.fire({title:"Error!",text:`${t.mensaje}`,icon:"error",fontsize:"5rem"})}catch(e){}}function mostrar_ver_producto(e){const n=e.target.dataset.id,o=productos_json.find((function(e){return e.id===n}));crearModal(),insertar_al_modal_producto_ver(o)}function insertar_al_modal_producto_ver(e){const{descripcion:n,disponible:o,id:t,imagen:a,nombre:r,precio:i,size:d}=e;document.querySelector(".contenido_modal").innerHTML=`\n    <div class="producto_div">\n            <div class="div_producto_uno">\n                <div class="imagen_producto_div">\n                    <img src="/img/productos/${a}" alt="imagenProducto" />\n                    \n                </div>\n                <div class="informacion_producto">\n                    <p >ID: <span>${t}</span></p>\n                    <p>Nombre: <span>${r}</span></p>\n                    <p>Precio: <span>${i}</span></p>\n                    <p>Tamaño: <span>${d}</span></p>\n                    <label htmlFor="">\n                    Disponible:\n                    <select disabled id="select_disponible" name="disponible">\n                        <option value="1">Si</option>\n                        <option value="0">No</option>\n                    </select>\n                    </label>\n                </div>\n            </div>\n            <div class="div_producto_dos">\n                 <p for="descripcion" ><span>Descripcion:</span></p>\n                  <textarea name="descripcion" id="descripcion" disabled>${n}</textarea>\n            </div>\n        <div class="producto_div_footer">\n            <button class="boton-eliminar" id="cancelar_boton">Volver</button>\n        </div>\n    </div>\n    `;document.getElementById("select_disponible").value=o;document.getElementById("cancelar_boton").addEventListener("click",eliminarModal)}function mostrarProductos(){cambiarTitulo("Productos"),crearTabla(["Imagen","Nombre","Precio","Actions"]),solicitud_get_por_url("/api/cuadros/getcuadros").then(llenarAndMostrarTablaProductos)}function cambiarTitulo(e){document.querySelector(".titulo_app").innerHTML=`<h1>${e}</h1>`}function crearTabla(e=[]){document.querySelector(".contenido").innerHTML='\n    <table class="tablaResumen id="tabla">\n    <thead>\n            <tr class="trHead">\n                \n            </tr>\n        </thead>\n        <tbody id="tbody">\n        \n        </tbody>\n    </table>\n    ';const n=document.querySelector(".trHead");e.forEach((e=>{const o=document.createElement("TD");o.textContent=e,n.appendChild(o)}))}function llenarAndMostrarTablaProductos(e){const n=document.getElementById("tbody");productos_json=e,e.forEach((e=>{const{descripcion:o,disponible:t,id:a,imagen:r,nombre:i,precio:d,size:c}=e,s=document.createElement("TR");s.innerHTML=`\n        <td><img src="/img/productos/${r}" alt="imagenProducto" /></td>\n        <td><p>${i}</p></td>\n        <td><p>$<span>${parseInt(d).toLocaleString()}</span></p></td>\n        <td>\n            <div class="actions">\n                <button data-id="${a}" class="boton-editar">Editar</button>\n                <button data-id="${a}" class="boton-ver">Ver</button>\n                <button data-id="${a}" class="boton-eliminar">Eliminar</button>\n            </div>\n        </td>\n        `,n.appendChild(s)})),evento_botones_action()}async function solicitud_get_por_url(e){try{const n=await fetch(e);return await n.json()}catch(e){console.log(e)}}function mostrar_editar_producto(e){const n=e.target.dataset.id,o=productos_json.find((function(e){return e.id===n}));crearModal(),insertar_al_modal_producto(o),document.querySelector(".formulario").addEventListener("submit",evento_formulario_actualizar)}async function evento_formulario_actualizar(e){e.preventDefault();const n=new FormData(e.target);try{const e=`${location.origin}/api/admin/actualizar_producto`,o={method:"post",body:n};crear_alerta_de_cargando();const t=await fetch(e,o),a=await t.json();if(eliminar_alerta_de_cargando(),a.resultado)Swal.fire({title:"Buen trabajo!",text:`${a.mensaje}`,icon:"success",fontsize:"5rem"}).then(eliminar_modal_and_mostrar_productos);else{const e=document.getElementById("mensaje_error");e.innerHTML="",a.alertas.forEach((n=>{const o=document.createElement("P");o.textContent=n,e.appendChild(o)}))}}catch(e){console.log(e)}}function eliminar_modal_and_mostrar_productos(){eliminarModal(),mostrarProductos()}function insertar_al_modal_producto(e){const{descripcion:n,disponible:o,id:t,imagen:a,nombre:r,precio:i,size:d}=e;document.querySelector(".contenido_modal").innerHTML=`\n    <form class="producto_div formulario" id="formulario" enctype="multipart/form-data">\n        \n            <div class="div_producto_uno">\n                <div class="imagen_producto_div">\n                    <img src="/img/productos/${a}" alt="imagenProducto" />\n                    \n                </div>\n                <div class="informacion_producto">\n                    <label htmlFor="">ID: ${t}</label>\n                    <input type="hidden" name="id" value="${t}" />\n                    <label htmlFor="nombre_editar">Nombre: <input name="nombre" id="nombre_editar" type="text" value="${r}" /></label>\n                    <label >Precio: <input name="precio" id="precio_editar" type="text" value="${i}" /></label>\n                    <label htmlFor="">\n                    Disponible:\n                    <select id="select_disponible" name="disponible">\n                        <option value="1">Si</option>\n                        <option value="0">No</option>\n                    </select>\n                    </label>\n                    <label htmlFor="">Tamaño: <input type="text" name="size" value="${d}" /></label>\n                </div>\n            </div>\n            <div class="div_producto_dos">\n                <input type="file" name="imagen" value="${a}" />\n                 <label for="descripcion" >Descripcion:</label>\n                  <textarea name="descripcion" id="descripcion" >${n}</textarea>\n            </div>\n        <div id="mensaje_error"></div>\n        <div class="producto_div_footer">\n            <input type="submit" value="Actualizar Datos" id="actualizar_datos" class="boton-ver" />\n            <button class="boton-eliminar" id="cancelar_boton">Cancelar</button>\n        </div>\n    </form>\n    `;document.getElementById("select_disponible").value=o;document.getElementById("cancelar_boton").addEventListener("click",eliminarModal)}function crearModal(){const e=document.createElement("DIV");e.classList.add("modal"),e.innerHTML='\n    <div class="contenido_modal"></div>\n    ',e.addEventListener("click",evento_para_cerrar_modal);document.querySelector("body").appendChild(e)}function evento_para_cerrar_modal(e){e.target.closest(".producto_div")||eliminarModal()}function eliminarModal(){document.querySelector(".modal").remove()}function mostrarDatosResumen(){getDatosResumen().then(imprimirDatosResumen)}function imprimirDatosResumen(e){document.querySelector("#numero_pedidos span").textContent=e.respuesta.cantidad_pedidos;document.querySelector("#numero_productos span").textContent=e.respuesta.cantidad_productos;document.querySelector("#numero_usuarios span").textContent=e.respuesta.cantidad_usuarios}async function getDatosResumen(){try{const e="http://localhost:3000/api/admin/get_info_resumen",n=await fetch(e);return await n.json()}catch(e){console.log(e)}}function actualizarPagina(){location.reload()}
//# sourceMappingURL=admin.js.map
