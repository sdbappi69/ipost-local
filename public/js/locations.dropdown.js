$(".js-example-basic-single").select2({
    placeholder : 'SELECT ONE',
    allowClear : true
}).trigger('change');

$(".js-example-basic-multiple").select2({
    placeholder : 'SELECT FROM LIST'
});

var $eventSelect = $(".js-country, .js-state, .js-city");

$eventSelect.on("select2:select", function (e) {
    console.log(e.params.data.id)
    if(e.params.data.id) {

        var dataType = $(this).attr('data-type');

        $(".js-"+populateDataTo(dataType)+" option").remove();
        $(".js-"+populateDataTo(dataType)).select2({
            placeholder : 'SELECT ONE'
        });

        var settings = {
            "async": true,
            "crossDomain": true,
            "url": site_path+"fetch-locations",
            "data": { search_id : e.params.data.id, search_model : searchInModel(dataType), attr_name : $(this).attr('name') },
            "method": "POST",
            "headers": {
                "accept": "application/json"
            }
        }

        $.ajax(settings).done(function (response) {
            //console.log(response);

            $(".js-"+populateDataTo(dataType)).append("<option value=''>SELECT ONE</option>");

            $.each(response, function(idx, obj) {
                $(".js-"+populateDataTo(dataType)).append("<option value="+obj.id+">"+obj.name+"</option>");
            });
        });
    }
});
$eventSelect.on("select2:unselect", function (e) { console.log(e.params.data.id) });

function populateDataTo(model)
{
    var model = model;

    if(model == 'Country') {
        return 'state';
    } else if(model == 'State') {
        return 'city';
    } else if (model == 'City') {
        return 'zone';
    }
}

function searchInModel(dataType)
{
    var dataType = dataType;

    if(dataType == 'Country') {
        return 'State';
    } else if(dataType == 'State') {
        return 'City';
    } else if (dataType == 'City') {
        return 'Zone';
    }

}