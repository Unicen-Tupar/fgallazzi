{extends file="layout-trastos-ajax.tpl"}

{block name = scriptJS}
<script type="text/javascript" src="js/jquery.form.min.js"></script>
<script type="text/javascript" src = "js/carrito.js"></script>
{/block}

{block name = body}

<div class="modal fade" id="modal-carrito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Trastos.com <small>Mi Carrito</small></h4>
      </div>
      <div class="modal-body">
        <h4>Productos</h4>

        <table class="table table-hover" id ="table-carrito">
          <thead>
            <tr>
              <th>
                &nbsp;
              </th>
              <th>
                Producto
              </th>
              
              <th>
                Vendedor
              </th>
              <th>
                Precio
              </th>
              <th>
                Cant.
              </th>
              <th>
                Total
              </th>
            </tr>  
          </thead>
          <tbody>
            {foreach from=$productos item = p}
              <tr id="{$p.id_producto}">
                <td>
                  <div class="carrito-delete">
                  <i class="glyphicon glyphicon-remove-circle"
                     onclick = "delProducto({$p.id_producto});"></i>
                  </div>
                </td>
                <td><a href="index.php?{$ACTION}={$ACTION_DETALLE}&{$ID_PRODUCTO}={$p.id_producto}">{$p.v_nombre_producto}</a></td>
              
                <td>{$p.v_usuario}</td>
              
                <td>${$p.f_precio_unidad}</td>

                <td name="n_cantidad">
                  <span>{$p.n_cantidad}</span>
                  <div class="carrito-up-down">
                    <i class="glyphicon glyphicon-chevron-up" 
                       onclick = "incCantidad({$p.id_producto});"></i>
                    <i class="glyphicon glyphicon-chevron-down"
                       onclick = "decCantidad({$p.id_producto});"></i>
                  </div>
                </td>
                <td name="f_total">${$p.n_cantidad * $p.f_precio_unidad}</td>
              </tr>    
            {/foreach}
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default"
                onclick="confirmarCompra()"  >Confirmar la compra</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
      </div>
    </div>
  </div>
</div>
{/block}