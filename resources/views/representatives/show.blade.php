<x-layout Title="Representatives">
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2>{{ $representative->name }}</H2>
            </div>
            <a href="{{ route('representatives.edit', $representative) }}" id="update-representative"><i class="bi bi-pencil-square fs-2"></i></a>
            <a href="{{route('representatives.index')}}" id="cancel-save-representative" title="Cancel Update"><i class="bi bi-x-square-fill fs-2"></i></a>
        </div>


        <div>
            <form id="representative-data" enctype="multipart/form-data">
                <div class="row">

                    <div class="form-group col-md-1">
                        <label for="representativeid">ID:</label>
                        <input type="text" id="representativename" name="name" value="{{ $representative->id }}"
                               readonly class="form-control-plaintext">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="representativeemail">Email:</label>
                        <input type="email" id="representativeemail" name="email" value="{{ $representative->email }}"
                               class="form-control-plaintext">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="representativeorganisation">Organisation:</label>
                        <input type="text" id="representativeorganisation" name="organisation" value="{{ $representative->represents->name }}"
                               readonly class="form-control-plaintext">
                    </div>

                    <div class="form-group col-sm-1 checkbox">
                        <input class="form-check-input" type="checkbox" value="1" id="representative-active" name="active"
                               @if( $representative->active )
                                   checked
                               @endif disabled>
                        <label class="form-check-label" for="representative-active">Active</label>
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group col-md-3">
                        <label for="representativeemail">Email:</label>
                        <input type="email" id="representativeemail" name="email" value="{{ $representative->tokens()->exists() }}"
                               class="form-control-plaintext">
                        @php
                        $token = $representative->tokens()->where('tokenable_id', $representative->id)
                        ->where('name', 'RepresentativeToken')->first();

                        if($token) {
                            $abilities = $token->abilities;
                            echo "Abilities for this token: " . implode(', ', $abilities);
                        } else {
                            echo "Token not found.";
                        }

                        @endphp
                    </div>

                </div>


            </form>
        </div>

    </div>
</x-layout>
