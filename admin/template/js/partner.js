$(function(){
	//省
    $('#province').live('change',function(){
        var province_id = $(this).val();
        show_city(province_id);
        $('#J_region').find('span').html('');
        $('#J_station_head_span').html('');
    });
    //市
    $('#city').live('change',function(){
        var city_id = $(this).val();
        show_area(city_id);
        if(city_id != -1){
            $('#J_region').find('span').html(city_id);
            _ajax_get_station_head(city_id);
        }else{
            $('#J_region').find('span').html('');
        }
    });
    //区
    $('#area').live('change',function(){
        //改变站点ID
        var area_id = $(this).val();
        var region_id = area_id;
        if(area_id != -1){
            $('#J_region').find('span').html(area_id);
        }else{
            var city_id = $('#city').val();
            if(city_id != -1){
                $('#J_region').find('span').html(city_id);
                region_id = city_id;
            }else{
                $('#J_region').find('span').html('');
            }
        }
        //获得站长ID
        if(region_id != -1){
            _ajax_get_station_head(region_id);
        }
    });
});
function _ajax_get_station_head(region_id){
    $.ajax({
        url: 'join.php?module=getStationHead',
        type: 'GET',
        data: {region_id:region_id,timestamp:new Date().getTime()},
        success: function(data){
            if(data > 0){
                $('#J_station_head_span').html(data);
            }else{
                $('#J_station_head_span').html(10000);
            }
        }
    });
}