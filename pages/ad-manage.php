<?php
function display_mistheme_advertise() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $adData = mistheme_get_allAds();
    ?>
    <div class="wrap">
        <?php
        if ( function_exists( 'wp_nonce_field' ) )
            wp_nonce_field( 'TableData_action', 'TableData_nonce' );
        ?>
        <div class="col-md-12">
            <div class="well" style="margin-top: 10px;">
                <a class="btn btn-primary btn-sm" href="<?php menu_page_url( 'ads-new-submenu', true ); ?>">
                    <span class="glyphicon glyphicon-plus"></span>
                    أضف إعلان جديد
                </a>
            </div>
            <table id="table">

            </table>
        </div>
        <script>
            var tableElement = jQuery('#table');
            tableElement.bootstrapTable({
                columns: [{
                    field: 'Ad_id',
                    title: 'ID',
                    visible: false,
                }, {
                    field: 'Ad_link_type',
                    title: '',
                    formatter: adLinkTypeFormatter,
                    width: '32px',
                },{
                    field: 'Ad_type',
                    title: 'النوع',
                    formatter: AdTypeFormatter,
                    width: '110px',
                    class: 'text-center',
                },{
                    field: 'Ad_ar_name',
                    title: 'الاسم بالعربية',
                    class: 'text-center',
                }, {
                    field: 'Ad_en_name',
                    title: 'الاسم بالانجليزية',
                    class: 'text-center',
                    visible: false,
                }, {
                    field: 'Ad_start_date',
                    title: 'تاريخ',
                    formatter: dateFormatter,
                    class: 'text-center',
                    width: '280px',
                }, {
                    field: 'Ad_id',
                    title: 'إعدادات',
                    formatter: optionFormatter,
                    width: '136px',
                    class: 'text-center',
                }
                ],
                ajax: 'adminTable',
                sidePagination: 'server',
                detailView: true,
                detailFormatter: detailFormatter,
                formatNoMatches: noMatchesFormatter,
            });

            function dateFormatter(value, row, index){
                var todayDate = new Date();
                    todayDate.setHours(2);
                    todayDate.setMinutes(0);
                    todayDate.setSeconds(0);
                    todayDate.setMilliseconds(0);
                var endDate = new Date(row.Ad_end_date);
                var expireBadge = '';
                if(!(endDate.getTime() == todayDate.getTime()) && endDate.getTime() < todayDate.getTime()){
                    expireBadge = '<span class="label label-default pull-left">منتهي</span>'
                }
                return ['<span class="pull-right">من ',
                    '<span class="date-start">',
                        row.Ad_start_date,
                    '</span>',
                    '<span> إلى </span>',
                    '<span class="date-end">',
                        row.Ad_end_date,
                    '</span></span>',
                    expireBadge,
                ].join('');

            }

            function detailFormatter(index, row) {

                var Ad_ok = '<span class="fa fa-check text-success"></span>';
                var Ad_no = '<span class="fa fa-times text-danger"></span>';
                var Ad_show_to_captain = row.Ad_show_to_captain == 1 ? Ad_ok:Ad_no;
                var Ad_show_to_user = row.Ad_show_to_user == 1 ? Ad_ok:Ad_no;
                var Ad_cap_notify = row.Ad_cap_notify == 1 ? Ad_ok:Ad_no;
                var Ad_user_notify = row.Ad_user_notify == 1 ? Ad_ok:Ad_no;
                var Ad_showonmap_captain = row.Ad_showonmap_captain == 1 ? Ad_ok:Ad_no;
                var Ad_showonmap_user = row.Ad_showonmap_user == 1 ? Ad_ok:Ad_no;
                var Advertiser_type;
                var Advertiser_rep_type;
                switch (Number(row.Advertiser_type)){
                    case 1:
                        Advertiser_type = "جهة حكومية";
                        break;
                    case 2:
                        Advertiser_type = "جهة خاصة";
                        break;
                    case 3:
                        Advertiser_type = "جهة خيرية";
                        break;
                    case 4:
                        Advertiser_type = "أخرى";
                        break;
                    default:
                        Advertiser_type = "-";
                }
                switch (Number(row.Advertiser_rep_type)){
                    case 1:
                        Advertiser_rep_type = "مباشر";
                        break;
                    case 2:
                        Advertiser_rep_type = "وسيط";
                        break;
                    case 3:
                        Advertiser_rep_type = "مندوب";
                        break;
                    default:
                        Advertiser_rep_type = "-";
                }
                var url = new Url(row.Advertiser_website);
                var locationArray = row.Ad_locations.split(':');
                var location;
                var locationObject = [];
                for (var i = 0; i < locationArray.length; i++) {
                    location = locationArray[i].split(',');
                    location = {lat: Number(location[0]), lng: Number(location[1])};
                    locationObject.push(location);
                }
                //console.log(locationObject);
                var Advertiser_website = '<a href="'+url.toString()+'" target="_blank">'+url.toString()+'</a>';
                var detailsHTML;
                var prioHTML = '<div class="adPriorityDetails">'
                for (var i = 1; i<6; i++){
                    if (i<= row.Ad_priority){
                        prioHTML += '<i class="fa fa-star fa-lg" style="color: #fde16d;" aria-hidden="true"></i>'
                    }else{
                        prioHTML += '<i class="fa fa-star-o fa-lg" aria-hidden="true"></i>'
                    }
                }
                prioHTML += '</div>'
                var mediaHTML;
                if (row.Ad_link_type == 1){
                    mediaHTML = '<img src="'+row.Ad_link+'" alt="..." class="img-responsive img-thumbnail">';
                }else {
                    mediaHTML = '<video width="100%" height="auto" class="img-thumbnail" controls>'+
                        '<source src="'+row.Ad_link+'">'+
                        'Your browser does not support the video tag.'+
                        '</video>';
                }
                var optionHTML = [
                        '<table class="table option-table">',
                            '<thead>',
                                '<tr>',
                                    '<th></th>',
                                    '<th class="text-center"><span class="fa fa-eye fa-lg"></span></th>',
                                    '<th class="text-center"><span class="fa fa-bell-o fa-lg"></span></th>',
                                    '<th class="text-center"><span class="fa fa-map-marker fa-lg"></span></th>',
                                    '<th class="text-center"><span class="fa fa-line-chart fa-lg"></span></th>',
                                '</tr>',
                            '</thead>',
                            '<tbody>',
                                '<tr>',
                                    '<td>الكابتن</td>',
                                    '<td class="text-center">'+Ad_show_to_captain+'</td>',
                                    '<td class="text-center">'+Ad_cap_notify+'</td>',
                                    '<td class="text-center">'+Ad_showonmap_captain+'</td>',
                                    '<td class="text-center">'+row.Ad_cap_view_no+'</td>',
                                '</tr>',
                                '<tr>',
                                    '<td>المستخدم</td>',
                                    '<td class="text-center">'+Ad_show_to_user+'</td>',
                                    '<td class="text-center">'+Ad_user_notify+'</td>',
                                    '<td class="text-center">'+Ad_showonmap_user+'</td>',
                                    '<td class="text-center">'+row.Ad_user_view_no+'</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',].join('');
                var mapHTML = [
                        '<div class="map-btn">',
                            '<a class="btn btn-primary btn-sm form-control" role="button" id="btn-map" data-target="#map-modal-'+Number(row.Ad_id)+'">',
                                '<span>إظهار المواقع على الخريطة</span>',
                            '</a>',
                            '<div class="modal fade" id="map-modal-' + Number(row.Ad_id) + '" tabindex="-1" role="dialog" data-location='+JSON.stringify(locationObject)+' data-map="map' + Number(row.Ad_id) + '">',
                                '<div class="modal-dialog modal-lg" role="document">',
                                    '<div class="modal-content" style="width: 100%;height: 450px">',
                                        '<div id="map' + Number(row.Ad_id) + '" class="mapContainer"></div>',
                                    '</div>',
                                '</div>',
                            '</div>',
                        '</div>',
                    ].join('');

                var advertiserHTML = [
                            '<div class="panel panel-default" id="advertiser-panel" style="margin-top: 15px;">',
                                '<div class="panel-body">',
                                    '<div class="clearfix">',
                                        '<h5 class="pull-right margin-null">الجهة المعلنة:</h5>',
                                        '<span class="label label-default pull-left">',Advertiser_type,'</span>',
                                    '</div>',
                                    '<div class="text-center">',row.Advertiser_name,'</div>',
                                '</div>',
                                '<div class="panel-footer" style="display: none;">',
                                    '<ul class="list-group">',
                                        '<li class="list-group-item"><span class="fa fa-phone"></span> ',row.Advertiser_phone,'</li>',
                                        '<li class="list-group-item"><span class="fa fa-envelope"></span> ',row.Advertiser_email,'</li>',
                                        '<li class="list-group-item"><span class="fa fa-map-marker"></span> ',row.Advertiser_address,'</li>',
                                        '<li class="list-group-item"><span class="fa fa-link"></span> ',Advertiser_website,'</li>',
                                    '</ul>',
                                '</div>',
                            '</div>',
                    ].join('');
                var advertiserRepHTML = [
                        '<div class="panel panel-default" id="advertiser-panel">',
                            '<div class="panel-body">',
                                '<div class="clearfix">',
                                    '<h5 class="pull-right margin-null">ممثل الجهة المعلنة:</h5>',
                                    '<span class="label label-default pull-left">',Advertiser_rep_type,'</span>',
                                '</div>',
                                '<div class="text-center">',row.Advertiser_rep_name,'</div>',
                            '</div>',
                            '<div class="panel-footer" style="display: none;">',
                                '<ul class="list-group">',
                                    '<li class="list-group-item"><span class="fa fa-phone"></span> ',row.Advertiser_rep_phone,'</li>',
                                    '<li class="list-group-item"><span class="fa fa-envelope"></span> ',row.Advertiser_rep_email,'</li>',
                                '</ul>',
                            '</div>',
                        '</div>',
                ].join('');
                detailsHTML = [
                    '<div class="">',
                        '<h4 class="text-center details-header">',row.Ad_en_name,'</h4>',
                        '<div class="col-md-4">',
                            '<div class="text-center prio-starts">',
                                prioHTML,
                            '</div>',
                            '<div>',
                                mediaHTML,
                            '</div>',
                        '</div>',
                        '<div class="col-md-4">',
                            optionHTML,
                            mapHTML,
                        '</div>',
                        '<div class="col-md-4">',
                            advertiserHTML,
                            advertiserRepHTML,
                        '</div>',
                    '</div>',
                ].join('');
                return detailsHTML;
            }

            function AdTypeFormatter (value, row, index){
                var typeTxt;
                switch (Number(value)){
                    case 1:
                        typeTxt = "فعالية";
                        break;
                    case 2:
                        typeTxt = "دعاية";
                        break;
                    case 3:
                        typeTxt = "عروض ترويجية";
                        break;
                    default:
                        typeTxt = "-";
                }
                return typeTxt;
            }
            function optionFormatter(value, row, index) {
                //console.log(value);
                return [
                    '<a class="btn btn-warning btn-xs" href="<?php menu_page_url( 'ads-new-submenu', true ); ?>' + '&id='+value+ '" id="editAd">',
                        '<i class="glyphicon glyphicon-pencil"></i> تعديل',
                    '</a>  ',
                    '<a class="btn btn-danger btn-xs" href="#" id="deleteAd" data-id="'+ value +'">',
                        '<i class="glyphicon glyphicon-remove"></i> حذف',
                    '</a>'
                ].join('');
            }
            function adLinkTypeFormatter(value, row, index){
                var adIcon;
                if(value == 2){
                    adIcon = "fa fa-file-movie-o fa-lg";
                }else{
                    adIcon = "fa fa-file-picture-o fa-lg";
                }
                return '<i class="'+adIcon+'"></i>';
            }
            function noMatchesFormatter(){
                return 'لا يوجد إعلانات للعرض '+'<a class="btn btn-primary btn-xs" href="<?php menu_page_url( 'ads-new-submenu', true ); ?>"><span class="glyphicon glyphicon-plus"></span> أضف إعلان جديد</a>' ;

            }
            function adminTable (params){
                var contents = {
                    action:	'AdminTableData',
                    nonce:	jQuery('#TableData_nonce').val(),
                };
                jQuery.post( admin_ajax.url, contents, function(data){
                    params.success(data);
                }, 'json');
            }
        </script>
        <script async defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaFIlw34FsDp1hvx4N7rhprs4Ya7_dnVU"></script>
        <script>
            var allMarkers = [];
            function myMap(element, location) {
                var mapCanvas = document.getElementById(element);
                var myCenter = new google.maps.LatLng(24.647017162630366,44.589385986328124);
                var mapOptions = {center: myCenter, zoom: 5,streetViewControl: false};
                map = new google.maps.Map(mapCanvas, mapOptions);
                allMarkers = [];
                if(location.length > 0){
                    for (var i = 0; i < location.length; i++){
                        placeMarker(location[i]);
                    }
                }
                showMapMarkers(map);
            };

            function showMapMarkers(map){
                for (var i = 0; i < allMarkers.length; i++){
                    allMarkers[i].setMap(map);
                    allMarkers[i].setLabel((i+1).toString());
                    var markerLatLng = allMarkers[i].getPosition();
                }
            }

            function placeMarker(location) {
                var marker = new google.maps.Marker({
                    position: location,
                    draggable: false,
                });
                allMarkers.push(marker);
            }

        </script>
    </div>
    <?php
}