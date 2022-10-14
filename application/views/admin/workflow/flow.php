<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    div.lineHorizontal {
        border-left: 3px dashed gray;
        height: 50px;
        left: 10%;
        margin-left: 10%;
    }

    div#openFlowsWrapper {
        margin-left: 5%;
        width: 10%;
        text-align: center;
        padding: 15px;
        border-radius: 50px;
        background-color: white;
        font-size: 18px;

    }

    div#openFlowsWrapper .btn {
        background-color: white;
    }

    .flow {
        margin-bottom: 0px;
    }

    .addflow {
        cursor: pointer;
        transition: transform .2s;
    }

    .addflow:hover .panel-body {
        border-color: black;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">

                <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo admin_url('workflow') ?>">Workflow</a></li>
                    <li class="breadcrumb-item active"><?php echo $workflow['name'] ?></li>
                </ol>
                </nav>

                <div class="panel_s flow">
                    <div class="panel-body">
                        <p class="text-muted"><?php echo _l('action') ?></p>
                        <h4 class="no-margin"><?php echo $workflow['name'] ?></h4>
                        <p><?php echo $workflow['description'] ?></p>
                    </div>
                </div>
                <div class="lineHorizontal"></div>
                <?php foreach ($workflow['flows'] as $flow) : ?>
                    <?php if (isset($workflow['services'][$flow->service])) : $service = $workflow['services'][$flow->service]; ?>
                        <div class="panel_s flow">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHcAAAB3CAMAAAAO5y+4AAAAbFBMVEX///8AAAD8/PzIyMi9vb3Nzc3f39/q6uq0tLT5+fnz8/Pt7e3FxcUMDAzn5+fCwsLW1taBgYFVVVUzMzNERESlpaU8PDyLi4taWloTExOVlZV7e3usrKxycnItLS2cnJxoaGglJSVLS0sbGxtvtC+wAAAFDUlEQVRoge1a67KyOgx1IyCgIggIosjF93/HT9lNSaFIguwzc2ZYP7Xpork3sNmsWLFixYoVS8B44T8l9PxtGVbZ+Y2sSJ1g5/45p+uXVfTTR3Nz9n/JaqdDTkCWeH/E6jSjpL8orOVJjXKCtMXltDBtQmFtmZc8czBu1iGqpezspv2t66zIE+ewPTllWjXX3r9HcxFaO1Z2fVT3YI8Thruz8rPKfFvgyI6yY2bqA9VOLnhZ7H9Lm6PdrqH9YeU2w7r+0r1uaK9wN7HYwur+ysgV0vCnswJM5GPOIrTEXYzbAifu9ojpaf/QEc+0cZejbhwxv5Zys7x6K8VznqAnAz6aEce7J0iXXFFD+nXB561m075SmCRmO/UdJFM+7UvVkL2OTBPvIBCzObSvvAlWqnhyhRB7zM3wJ9AXqxMIQCqYSbvZQPG8cJpd8Aslgu5xXUd0c4OJ73RaC7SMn7VgeijoLKIfGCoatg3orSFvAz5CztM2UKDfTJm9yFnXFwIxVQBq/bb7yaslLz0HQV2hOudRGAb9hPrYKzkVgN6IvghehT0Id08JlXcDFwzaavAglDL2iJYRkeAUNEXXQztuMS+2+2e4QoBUWuBs2P1NhfdM5YWi1kyvlA3zcz/8DUBp8RQ5ylphXiXqTgrtkcwLHk0xcKZx/kDhpUew8fiVoCTXemjejXFEtBFjpiAKDKFDAx9UVYM6aVZtTMkaElm1VrMScugDgxbaJYJDi2zVa0GNGmhZvTQEfjS9Urhu3EtKss+jB+8b4hTH6RRn6jXjylEDo3/oAmGa1xk5lgxhej1CvNMTvTHe7pbGaW3pvCN6fpV+qWnGfQlq6rSeR/wK7cG5fQiZ5/TKQBtHLbqRHbkSilNcpleKMvjU5f4ua/WbuzH7JeTgc0d2bv97SGK1Oc2fhT57CmekJJvogw1Rv4PrVeuLZ92TxnRPFMrUp3K7I47s/o/DaSy0v5TWPf/oCp1Tv47sKZu/hUz96qnJ1xsilY81FbjFu+bv3KUMJy+Kq0MEEGilY43lYQv3AMfCwuPB9y94bfzJZn2IxaO+PzGNRiEFNyRaKQHljOb/3XmM84WHZieKeTv3HO+2jXCct0LrxJCDUPVbPDSW6uEwyoviHkon9UKVDLcYwL3paY+o2YS5HbX/BI/+7P52peNFzwoRR2+3wXwT9yl7eGasInB7et8rs+GUhtxEce0zzjVgLU57AgepJlcavnyZkimdNcQuawoNM+Sa8obV8K2DebDUcDcy8qNjnLku0Yd8D0O+O74B52X1yhjyXsObmUNaoCW4IeS9Nea9CheBRGjHtLBlyWLOVUXozZp546aEPnJSBFlXToluOMB1SxiHzPpOQt4c+WYSsce7cv7C63JnzZ7VXwfW8QJaUbG6gWLNfmsFXaBwRtd3qsvPw5nW+g4VigcrYbRAXaCxN29QRpsJN/PKZ0d74dPC8LUwTqn6yUbjjKeBfYr7zPOM9zAeaOpHg1BbXry72s2GfNb+EHSIKrHwaXwzv/RWzPt65EOz2OFSFWF4KzKNUrKZ77qew60YqOe+5vaHe12rUtvEaVaWs7/Fuvd2OqdtzggIzI/8i8+wim6faxOeuqzz6eurNxrnm89UDNgmKsx+7HvbQhtcr3Y9Lue/wmzRVrFr5QT6tGgE96o+KpzXKBw8IR/5T5NMPfo+MJ0yDcM0T5yTvcxXhSPnXLFixYoVK1as+L/hH18eNwlNDMBOAAAAAElFTkSuQmCC" height="100px" width="100px">
                                    </div>
                                    <div class="col-xs-9">
                                        <div style="display: flex;justify-content: space-between;">
                                            <p class="text-muted"><?php echo $service['type'] ?></p>
                                            <div>
                                                <div class="onoffswitch">
                                                    <input type="checkbox" data-switch-url="<?= admin_url('workflow/updateFlowStatus') ?>" name="onoffswitch" class="onoffswitch-checkbox" id="<?= $flow->id ?>" data-id="<?= $flow->id ?>" <?= ($flow->inactive == 0) ? "checked" : "" ?>>
                                                    <label class="onoffswitch-label" for="<?= $flow->id ?>"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <h4><?php echo $service['name'] ?></h4>
                                        <p class="no-margin"><?php echo $service['description'] ?></p>
                                        <a href="<?php echo admin_url('workflow/configure/' . $flow->id) ?>" class="btn btn-primary" style="float: right;">Configure</a>
                                        <a href="#" data-flow-id="<?php echo $flow->id ?>" class="btn btn-danger delete-flow" style="float: right;margin-right:10px">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lineHorizontal"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div id="openFlowsWrapper">
                    <button class="btn btn-dark" id="openFlows"><i class="fa fa-plus" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="flowsModal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <?php foreach ($workflow['services'] as $service_name => $service) : ?>
                        <div class="col-md-3">
                            <div class="panel_s addflow no-margin" data-flow-name="<?php echo $service_name ?>">
                                <div class="panel-body" style="padding: 10px;">
                                    <div class="">
                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHcAAAB3CAMAAAAO5y+4AAAAbFBMVEX///8AAAD8/PzIyMi9vb3Nzc3f39/q6uq0tLT5+fnz8/Pt7e3FxcUMDAzn5+fCwsLW1taBgYFVVVUzMzNERESlpaU8PDyLi4taWloTExOVlZV7e3usrKxycnItLS2cnJxoaGglJSVLS0sbGxtvtC+wAAAFDUlEQVRoge1a67KyOgx1IyCgIggIosjF93/HT9lNSaFIguwzc2ZYP7Xpork3sNmsWLFixYoVS8B44T8l9PxtGVbZ+Y2sSJ1g5/45p+uXVfTTR3Nz9n/JaqdDTkCWeH/E6jSjpL8orOVJjXKCtMXltDBtQmFtmZc8czBu1iGqpezspv2t66zIE+ewPTllWjXX3r9HcxFaO1Z2fVT3YI8Thruz8rPKfFvgyI6yY2bqA9VOLnhZ7H9Lm6PdrqH9YeU2w7r+0r1uaK9wN7HYwur+ysgV0vCnswJM5GPOIrTEXYzbAifu9ojpaf/QEc+0cZejbhwxv5Zys7x6K8VznqAnAz6aEce7J0iXXFFD+nXB561m075SmCRmO/UdJFM+7UvVkL2OTBPvIBCzObSvvAlWqnhyhRB7zM3wJ9AXqxMIQCqYSbvZQPG8cJpd8Aslgu5xXUd0c4OJ73RaC7SMn7VgeijoLKIfGCoatg3orSFvAz5CztM2UKDfTJm9yFnXFwIxVQBq/bb7yaslLz0HQV2hOudRGAb9hPrYKzkVgN6IvghehT0Id08JlXcDFwzaavAglDL2iJYRkeAUNEXXQztuMS+2+2e4QoBUWuBs2P1NhfdM5YWi1kyvlA3zcz/8DUBp8RQ5ylphXiXqTgrtkcwLHk0xcKZx/kDhpUew8fiVoCTXemjejXFEtBFjpiAKDKFDAx9UVYM6aVZtTMkaElm1VrMScugDgxbaJYJDi2zVa0GNGmhZvTQEfjS9Urhu3EtKss+jB+8b4hTH6RRn6jXjylEDo3/oAmGa1xk5lgxhej1CvNMTvTHe7pbGaW3pvCN6fpV+qWnGfQlq6rSeR/wK7cG5fQiZ5/TKQBtHLbqRHbkSilNcpleKMvjU5f4ua/WbuzH7JeTgc0d2bv97SGK1Oc2fhT57CmekJJvogw1Rv4PrVeuLZ92TxnRPFMrUp3K7I47s/o/DaSy0v5TWPf/oCp1Tv47sKZu/hUz96qnJ1xsilY81FbjFu+bv3KUMJy+Kq0MEEGilY43lYQv3AMfCwuPB9y94bfzJZn2IxaO+PzGNRiEFNyRaKQHljOb/3XmM84WHZieKeTv3HO+2jXCct0LrxJCDUPVbPDSW6uEwyoviHkon9UKVDLcYwL3paY+o2YS5HbX/BI/+7P52peNFzwoRR2+3wXwT9yl7eGasInB7et8rs+GUhtxEce0zzjVgLU57AgepJlcavnyZkimdNcQuawoNM+Sa8obV8K2DebDUcDcy8qNjnLku0Yd8D0O+O74B52X1yhjyXsObmUNaoCW4IeS9Nea9CheBRGjHtLBlyWLOVUXozZp546aEPnJSBFlXToluOMB1SxiHzPpOQt4c+WYSsce7cv7C63JnzZ7VXwfW8QJaUbG6gWLNfmsFXaBwRtd3qsvPw5nW+g4VigcrYbRAXaCxN29QRpsJN/PKZ0d74dPC8LUwTqn6yUbjjKeBfYr7zPOM9zAeaOpHg1BbXry72s2GfNb+EHSIKrHwaXwzv/RWzPt65EOz2OFSFWF4KzKNUrKZ77qew60YqOe+5vaHe12rUtvEaVaWs7/Fuvd2OqdtzggIzI/8i8+wim6faxOeuqzz6eurNxrnm89UDNgmKsx+7HvbQhtcr3Y9Lue/wmzRVrFr5QT6tGgE96o+KpzXKBw8IR/5T5NMPfo+MJ0yDcM0T5yTvcxXhSPnXLFixYoVK1as+L/hH18eNwlNDMBOAAAAAElFTkSuQmCC" height="80px" width="80px">
                                    </div>
                                    <div class="">
                                        <p class="text-muted text-small"><?php echo $service['name'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    function addService(service) {
        var action = '<?php echo $workflow['action'] ?>';
        $.ajax({
            url: '<?= admin_url('workflow/addFlow') ?>',
            type: "post",
            data: {
                'action': action,
                'service': service
            },
            dataType: "json",
            success: function(response) {
                if (response.success == true) {
                    alert_float('success', response.msg);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert_float('warning', response.msg);
                }
            },
        })
    }
    $('#openFlows').click(function() {
        $('#flowsModal').modal('show');
    });
    $('.addflow').click(function() {
        var service = $(this).attr('data-flow-name');
        addService(service);
    });

    $('.delete-flow').click(function(e){
        e.preventDefault();
        if(confirm("Do you want to delete this?")){
            var flowid =$(this).attr('data-flow-id');
            var url ='<?php echo admin_url('workflow/deleteflow/') ?>'+flowid;
            $.ajax({
                url: url,
                type: "post",
                dataType: "json",
                success: function(response) {
                    if (response.success == true) {
                        alert_float('success', response.msg);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    }
                },
            });
        }
        
    });
</script>
</body>

</html>