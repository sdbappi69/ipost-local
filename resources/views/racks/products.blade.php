<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
  <h4 class="modal-title">Rack Products</h4>
</div>
<div class="modal-body">
   <table class="table table-bordered table-hover" id="modalTable">
      <thead class="flip-content">
           <th>Product ID</th>
           <th>Order ID</th>
           <th>Sub Order ID</th>
           <th>Product Title</th>
           <th>Width</th>
           <th>Height</th>
           <th>Length</th>
           <th>Qnty</th>
      </thead>
      <tbody>
            @foreach( $rack_products as $product )
               <tr>
                  <td><a class="label label-success">{{ $product->product_unique_id }}</a></td>
                  <td><a class="label label-success">{{ $product->unique_order_id }}</a></td>
                  <td><a class="label label-success">{{ $product->unique_suborder_id }}</a></td>
                  <td>{{ $product->product_title }}</td>
                  <td>{{ $product->width }}</td>
                  <td>{{ $product->height }}</td>
                  <td>{{ $product->length }}</td>
                  <td>{{ $product->quantity }}</td>
               </tr>
            @endforeach
      </tbody>
   </table>
</div>
<div class="modal-footer">
  <button type="button" class="btn green btn-outline" data-dismiss="modal">Close</button>
  <!-- <button type="button" class="btn green">Save changes</button> -->
</div>
