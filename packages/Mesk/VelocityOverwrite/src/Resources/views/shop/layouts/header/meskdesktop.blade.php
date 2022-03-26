
<div>
    <mega-menu-component
        main-sidebar=true
        id="sidebar-level-0"
        url="{{ url()->to('/') }}"
        category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 7 }}"
        add-class="category-list-container pt10">
    </mega-menu-component>
</div>


