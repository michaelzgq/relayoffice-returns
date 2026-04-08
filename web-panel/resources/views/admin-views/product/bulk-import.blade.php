@extends('layouts.admin.app')

@section('title',\App\CPU\translate('product_bulk_import'))

@section('content')
    <div class="content container-fluid">

        <h1 class="mb-4">{{\App\CPU\translate('Product_Bulk_Import')}}</h1>
        <form class="product-form" action="{{route('admin.product.bulk-import')}}" method="POST"
            enctype="multipart/form-data" id="store-or-update-data">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-10 p-4 h-100">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <h2 class="mb-2">{{\App\CPU\translate('Step_1')}}</h2>
                                            <div class="fs-12">
                                                {{\App\CPU\translate('Download_Excel_File')}}
                                            </div>
                                        </div>
                                        <img width="60" height="60" src="{{asset('/assets/admin/img/bulk-import-1.png')}}" alt="">
                                    </div>
                                    <h5 class="mb-3 fs-12">{{ \App\CPU\translate('Instruction') }}</h5>
                                    <ul class="m-0 pl-4 fs-12">
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('Download the template file and fill it with accurate product data.') }}
                                        </li>
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('You can use the example file to better understand the required format.') }}
                                        </li>
                                        <li>
                                            {{ \App\CPU\translate('Once completed, upload the file using the form below.') }}
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-10 p-4 h-100">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <h2 class="mb-2">{{\App\CPU\translate('Step_2')}}</h2>
                                            <div class="fs-12">
                                                {{\App\CPU\translate('Match_Spread_sheet_data_according_to_instruction')}}
                                            </div>
                                        </div>
                                        <img width="60" height="60" src="{{asset('/assets/admin/img/bulk-import-2.png')}}" alt="">
                                    </div>
                                    <h5 class="mb-3 fs-12">{{ \App\CPU\translate('Instruction') }}</h5>
                                    <ul class="m-0 pl-4 fs-12">
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('Carefully review your data using the provided template.') }}
                                        </li>
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('Ensure all cells are filled correctly. Blank cells or incorrect data types may cause the import to fail.') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded-10 p-4 h-100">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <h2 class="mb-2">{{\App\CPU\translate('Step_3')}}</h2>
                                            <div class="fs-12">
                                                {{\App\CPU\translate('Validate data and complete import')}}
                                            </div>
                                        </div>
                                        <img width="60" height="60" src="{{asset('/assets/admin/img/bulk-import-1.png')}}" alt="">
                                    </div>
                                    <h5 class="mb-3 fs-12">{{ \App\CPU\translate('Instruction') }}</h5>
                                    <ul class="m-0 pl-4 fs-12">
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('After uploading, edit each product to add images and select options as needed.') }}
                                        </li>
                                        <li class="mb-2">
                                            {{ \App\CPU\translate('Use the correct Brand ID and Category ID from the available lists to ensure proper classification.') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                    </div>
                    <div class="text-center mt-5">
                        <h4 class="mb-3">{{\App\CPU\translate('download_spreadsheet_template')}}</h4>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('admin.product.bulk-export', ['without_product_code' => true]) }}"  class="btn btn-outline-primary border-primary fw-semibold">{{ \App\CPU\translate('With_Current_Data') }}</a>
                            <a href="{{ asset('assets/product_bulk_format.xlsx') }}" download="" class="btn btn-primary fw-semibold">{{ \App\CPU\translate('Without_Any_Data') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="mt-5">
                        <h4 class="mb-4 text-center">{{ \App\CPU\translate('Import_Items_file') }}</h4>
                        <div class="uploadDnD">
                            <div class="inputDnD position-relative max-w-510px m-auto border-dashed rounded-10">
                                <div class="upload-text p-4">
                                    <div class="mb-2">
                                        <img src="{{asset('/assets/admin/img/bulk-import-1.png')}}" alt="">
                                    </div>
                                    <div class="filename line-limit-1 title opacity-lg" data-default="{{\App\CPU\translate('drag_&_drop_file_or_browse_file')}}">{{\App\CPU\translate('drag_&_drop_file_or_browse_file')}}</div>
                                    <div class=" line-limit-1 title opacity-lg" >{{ getFileFormatSizeTranslatedText('.xlsx, .xls') }}</div>
                                </div>
                                <input type="file" name="products_file" class="form-control-file action-upload-section-dot-area" id="products_file" accept=".xlsx, .xls" data-max-upload-size="{{ readableUploadMaxFileSize('file') }}">
                                <span class="error-text" data-error="products_file"></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end flex-wrap gap-3 mt-4">
                        <button type="reset" class="btn btn-light fw-semibold min-w-120px">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit"  class="btn btn-primary fw-semibold min-w-120px">{{ \App\CPU\translate('submit') }}</button>
                    </div>
                </div>
            </div>
         </form>
    </div>
@endsection

@push('script_2')
    <script>
        $(".action-upload-section-dot-area").on("change", function () {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = () => {
                    let imgName = this.files[0].name;
                    $(this).closest(".uploadDnD").find('.filename').text(imgName);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        $('.product-form').on('reset', function () {
            $(this).find('.filename').each(function () {
                const defaultText = $(this).data('default');
                $(this).text(defaultText);
            });
        });
    </script>
@endpush
