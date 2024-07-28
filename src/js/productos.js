(function(){

    redireccionar_producto();


    function redireccionar_producto(){
        const productos = document.querySelectorAll('.producto_div');
        productos.forEach(producto=>{
            producto.addEventListener('click', function(e){
                const id = e.currentTarget.dataset.id;
                window.location.href = `/producto?id=${id}`;
            })
        })
    }
    
})();