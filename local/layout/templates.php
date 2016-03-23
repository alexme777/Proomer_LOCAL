<script type='text/ejs' id='tooltipDrop'>
    <div class="tooltip-drop"><%== description %></div>
</script>

<script type='text/ejs' id='preloaderSvg'>
    <div class="preloaderContent">
        <svg width="240" height="150" viewBox="0 0 240 150">
            <defs>
                <filter id="goo">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="7" result="blur" />
                    <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 17 -7" result="cm" />
                    <feComposite in="SourceGraphic" in2="cm">
                </filter>
            </defs>
            <g filter="url(#goo)">
                <ellipse id="drop" cx="50" cy="90" rx="20" ry="20" fill-opacity="1" fill="#1d70a4"/>
                <ellipse id="drop2"cx="50" cy="90" rx="20" ry="20" fill-opacity="1" fill="#1d70a4"/>
            </g>
        </svg>
    </div>
</script>

<script type='text/ejs' id='newRoomBlock'>
    <div class="room" data-room-id="<%= newRoom.id %>" title="<%= newRoom.name %>">
        <span><%= newRoom.name %> — <%= newRoom.square %> М<sup>2</sup></span>
        <a href="javascript:void(0);" class="delete js-delete"></a>
    </div>
</script>

<script type="text/ejs" id="sidebarBasketItem">
    <div class="side-cart-item">
        <div class="remove js-remove" data-element-id="<%= item.ID %>" data-product-id="<%= item.PRODUCT_ID %>"></div>
        <a class="image" href="<%= item.DETAIL_PAGE_URL %>" style="background-image: url('<%= item.IMAGE_SRC %>')"></a>
        <a class="title" href="<%= item.DETAIL_PAGE_URL %>"><%= item.NAME %></a>
        <div class="price">
            <span>Цена</span>
            <span><%= item.PRICE %> <i class="ruble"></i></span>
        </div>
    </div>
</script>