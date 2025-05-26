@extends('layout')

@section('title', 'DANH SÁCH ĐƠN HÀNG')

@section('content')

<main>
  <section class="page-main">
    <div class="donhang">
      <div class="header-filter-shared ">
        <ul>
          <li><a href="{{ route('donhang', ['orderquery' => 'all']) }}"
              class="{{ request()->routeIs('donhang') && request()->query('orderquery') == 'all' ? 'active' : ''  }}">Tất
              cả</a></li>
          <li><a href="{{ route('donhang', ['orderquery' => '3']) }}"
              class="{{ request()->routeIs('donhang') && request()->query('orderquery') == '3' ? 'active' : ''  }}">Hoàn
              Thành</a></li>
          <li><a href="{{ route('donhang', ['orderquery' => '1']) }}"
              class="{{ request()->routeIs('donhang') && request()->query('orderquery') == '1' ? 'active' : ''  }}">Đang
              xử lý</a></li>
          <li><a href="{{ route('donhang', ['orderquery' => '2']) }}"
              class="{{ request()->routeIs('donhang') && request()->query('orderquery') == '2' ? 'active' : ''  }}">Đang
              Giao</a></li>
          <li><a href="{{ route('donhang', ['orderquery' => '0']) }}"
              class="{{ request()->routeIs('donhang') && request()->query('orderquery') == '0' ? 'active' : ''  }}">Đã
              Hủy</a></li>
        </ul>
      </div>
    </div>

    <div class="h1-key">
      <form method="GET" class="glass-us">
        <div class="sr-container">
          <input type="text" name="key" placeholder="ID Đơn Hàng.." value="{{ request()->key }}">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
      </form>

      <div class="filter-addbtn-right">

        <form method="GET" id="thutu1" class="xs-us">
          <div class="sr-container">
            <select name="thutu" id="thutu">
              <option value="" {{ request('thutu')=='' ? 'selected' : '' }}>Đơn hàng mới nhất</option>
              <option value="1" {{ request('thutu')=='1' ? 'selected' : '' }}>Trạng thái</option>
              <option value="2" {{ request('thutu')=='2' ? 'selected' : '' }}>Tổng tiền</option>
            </select>
            <input type="hidden" name="key" value="{{ request('key') }}">
            <input type="hidden" name="show" value="{{ request('show') }}">
            <input type="hidden" name="orderquery" value="{{ request('orderquery') }}">
          </div>
        </form>
      </div>
    </div>
    <div class="grid-donhang">
      <div class="grid-tieudedh grid-order-layout grid_table_th_shared table__th">
        <div>MDH </div>
        <div>Phương Thức</div>
        <div>Trạng Thái</div>
        <div>Thanh Toán</div>
        <div>Điện Thoại</div>
        <div>Địa Chỉ</div>
        <div>Ngày Đặt</div>
        <div>Tổng Tiền</div>

      </div>
      @foreach($order as $dh)
      @php
      $time = \Carbon\Carbon::parse($dh->order_date)->setTimezone(config('app.timezone'));
      @endphp
      <div class="grid-rowdh grid-order-layout grid_table_tb_shared subtable_tr modonhang-user">
        <div class="hienthi_detail bot-icon hover_scale_btn" data-order-id="{{$dh->id}}"><i class="fa-solid fa-plus "></i>{{$dh->id}}
        </div>
        <div class="left">{{$dh->payment->payment_method}}</div>
        <div class="order-status-container">
          @if($dh->status == 1 && $dh->thanh_toan == 1)
          <form id="form-{{$dh->id}}" class="order_form_status" action="{{route('orders.updateStatus')}}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{$dh->id}}">
            <input type="hidden" name="status" id="hidden-status-{{$dh->id}}">
            <div class="status-flex">



              <div class="right-action">
                <button class="button border-blue change-stauts_2" data-order-status="{{$dh->id}}" data-status="2">Giao
                  hàng</button>
              </div>

              <div
                class="center-status {{$dh->status == 1 ? 'vang' : ($dh->status == 2 ? 'blue' : ($dh->status == 3 ? 'gr' : 'red'))}}"
                data-order-status-id="{{$dh->id}}">
                {{$dh->status == 1 ? 'Đang xử lý' : ($dh->status == 2 ? 'Đang giao' : ($dh->status == 3 ? 'Hoàn thành' :
                'Hủy'))}}
              </div>

              <div class="left-action">
                <button class="button border-red change-stauts_1" data-order-status="{{$dh->id}}"
                  data-status="0">Hủy</button>
              </div>

            </div>
          </form>
          @elseif($dh->status == 2 && $dh->thanh_toan == 1)
          <form id="form-{{$dh->id}}" class="order_form_status single-action" action="{{route('orders.updateStatus')}}"
            method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{$dh->id}}">
            <input type="hidden" name="status" id="hidden-status-{{$dh->id}}">
            <div class="status-flex">
              <div class="center-status blue" data-order-status-id="{{$dh->id}}">
                Đang giao
              </div>
              <div class="right-action">

                <button class="button border-green change-stauts" data-order-status="{{$dh->id}}" data-status="3">Hoàn
                  Thành</button>
              </div>
            </div>
          </form>
          @else
          <div class="center-status {{$dh->status == 3 ? 'gr' : 'red'}}">
            {{$dh->status == 3 ? 'Hoàn thành' : 'Hủy'}}
          </div>
          @endif
        </div>
        <div class="{{$dh->thanh_toan == 0 ? 'vang' : 'gr'}}">{{$dh->thanh_toan == 0 ? 'Chưa thanh toán' : 'Đã thanh
          toán'}}</div>
        <div>{{$dh->phone}}</div>
        <div class="left">{{$dh->address }}</div>
        <div>
          <div>{{$time->diffForHumans()}}</div>
          <div class="subtable_th">{{$time->isoFormat('D MMMM')}}</div>
        </div>
        <div>{{number_format($dh->total_price,0,'.','.')}}đ</div>
      </div>

      {{-- --}}
      <div class="an-dh chitiet_donhang" id="an-dh-{{$dh->id}}" style="margin: 0 50px; font-size: 14px">
        <div class="ct-dh" style="font-size: 16px">Chi tiết đơn hàng</div>

      </div>
      <div class="grid-suborder-layout grid_table_th_shared_sb th_donhang subtable_th" id="detail-{{$dh->id}}"
        style="margin: 0 50px;font-weight: 600; font-size: 14px">
        <div></div>
        <div>MCT</div>
        <div class="left">Tên Sản Phẩm</div>
        <div>Giá</div>
        <div>Số lượng</div>
        <div class="actions-d grid-css ct">
          <a href="{{route('donhang_chitiet', ['id' => $dh->id])}}" title="Xem chi tiết" class="border-green hover_scale_btn">
            <i class="fa-solid fa-eye"></i>
          </a>
        </div>
        <div></div>
      </div>
      @foreach($dh->orders_detail as $detail)
      <div class="grid-suborder-layout grid_table_tb_shared_sb subtable_tr detail_donhang" id="details-{{$dh->id}}"
        style="margin: 0 50px; padding:20px 0; font-size: 14px; ">
        <div></div>
        <div>MCT{{$detail->productVariant->id}}</div>
        <div class="left">
          <div>{{$detail->productVariant->product->name}} ({{$detail->productVariant->option}})</div>
        </div>
        <div>{{number_format($detail->price,0,'.','.')}}</div>
        <div>{{$detail->quantity}}</div>

      </div>

      @endforeach

      @endforeach
    </div>
    <div class="end-user">
      <div class="left">
        <form method="GET" id="show1" class="show-sl">
          <label for="">Hiển Thị:</label>
          <select name="show" id="show">
            <option value="10" {{request('show')==10 ? 'selected' : '' }}>10</option>
            <option value="2" {{request('show')==2 ? 'selected' : '' }}>2</option>
            <option value="30" {{request('show')==30 ? 'selected' : '' }}>30</option>
            <option value="50" {{request('show')==50 ? 'selected' : '' }}>50</option>
            <option value="100" {{request('show')==100 ? 'selected' : '' }}>100</option>
            <option value="150" {{request('show')==150 ? 'selected' : '' }}>150</option>
          </select>
          <!-- giữ lại tham số -->
          <input type="hidden" name="key" value="{{ request('key') }}">
          <input type="hidden" name="thutu" value="{{ request('thutu') }}">
          <input type="hidden" name="orderquery" value="{{ request('orderquery') }}">
        </form>

      </div>
      <div class="right">
        @if ($order->lastPage() > 1)
        <ul>
          <!-- nút left -->
          <li class="{{$pageht}} == 1 ? 'disable' : ''">
            <a href="{{$order->appends(request()->query())->previousPageUrl()}}"><i
                class="fa-solid fa-caret-left"></i></a>
          </li>
          <!-- các trang -->
          @if ($start > 1)
          <li><a href="{{$order->appends(request()->query())->url(1)}}">1</a>
          </li>
          @if($start > 2)
          <li class="disabled"><span>...</span></li>
          @endif
          @endif
          @for ($i = $start; $i <= $end; $i++) <li class="{{($pageht == $i) ? 'active' : ''}}"><a
              href="{{$order->appends(request()->query())->url($i) }}">{{$i}}</a></li>
            @endfor


            @if ($end < $lapa) @if ($end < $lapa - 1) <li class="disabled"><span>...</span></li>
              @endif
              <li><a href="{{$order->appends(request()->query())->url($lapa)}}">{{$lapa}}</a></li>
              @endif

              <li class="{{($pageht == $lapa) ? 'disabled' : ''}}">
                @if($pageht == $lapa)
                <a href="{{$order->appends(request()->query())->url(1)}}"><i class="fa-solid fa-caret-right"></i></a>
                @else
                <a href="{{$order->appends(request()->query())->nextPageUrl()}}"><i
                    class="fa-solid fa-caret-right"></i></a>
                @endif
              </li>
        </ul>
        @endif
      </div>
    </div>
  </section>
</main>
<script src="{{ asset('/js/Alerts.js') }}"></script>
@endsection