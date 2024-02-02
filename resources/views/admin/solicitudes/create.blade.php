<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">SOLICITUD DE NUTRICIÓN PARENTERAL</h1>
    </div>
    <form action="{{ route('admin.solicitudes.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf

        <x-validation-errors class="mb-4" />

        <div class="flex gap-4">
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Paciente*:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Servicios*:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Cama:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Piso:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
        </div>
        <div class="flex gap-4">
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Registro:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Diagnóstico:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
        </div>
        <div class="flex gap-4">
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Peso:
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Sexo:
                </x-label>
                <x-select class="w-full" name="category_id">

                    <option value="">Femenino</option>
                    <option value="">Masculino</option>
                </x-select>
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Fecha de nacimiento*:
                </x-label>
                <x-input-solicitud type="date" value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
        </div>
        <div class="flex gap-4">
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Vía de administración*:
                </x-label>
                <x-select class="w-full" name="category_id">

                    <option value="">Central</option>
                    <option value="">Periférica</option>
                </x-select>
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Tiempo de infusión (h):
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
        </div>
        <div class="flex gap-4">
            <div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Sobrellenado* (mL):
                    </x-label>
                    <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
                </div>
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    Vol. Total* (mL):
                </x-label>
                <x-input-solicitud value="{{ old('') }}" name="" class="w-full" placeholder="" />
            </div>
            <div class="mb-4 flex items-baseline gap-2">
                <x-label class="mb-2">
                    NPT*:
                </x-label>
                <x-select class="w-full" name="category_id">

                    <option value="">RNPT</option>
                    <option value="">LACT</option>
                    <option value="">INF</option>
                    <option value="">ADOL</option>
                    <option value="">ADULT</option>
                </x-select>
            </div>
        </div>

        <h2>MACRONUTRIENTES:</h2>
        <hr>
        <div class="flex flex-row gap-4 items-center">
            <div>
                <h3>AMINOÁCIDOS</h3>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2 w-60">
                            Aminoácidos Adulto 10%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-40"
                            placeholder="" /><span>g/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Aminoácidos pediátricos 10%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Aminoácidos de cadena ramificada 8%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Aminoácidos esenciales 5.4%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g/Kg</span>
                    </div>
                </div>
            </div>
            <div>
                <h3>CARBOHIDRATOS:</h3>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Dextrosa 50%
                            </x-label>
                            <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                                placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                </div>
                <H3>LÍPIDOS:</H3>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                MCT/LCT 20%
                            </x-label>
                            <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                                placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                SMOF 20%
                            </x-label>
                            <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                                placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2>ELECTROLITOS</h2>
        <hr>
        <div class="flex flex-row gap-4 items-start">
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Cloruro de Sodio (3 mEq/mL Na)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Acetato de sodio (4 mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Sulfato de Magnesio (0.81mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Cloruro de Potasio (4 mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Acetato de Potasio (2mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Fosfato de Potasio (2mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Gluconato de Calcio (0.465 mEq/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mEq/Kg</span>
                    </div>
                </div>
            </div>
        </div>
        <h2>ADITIVOS:</h2>
        <hr>
        <div class="flex flex-row gap-4 items-start">
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Ácidos Grasos Omega 3 10%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mL</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Albúmina 25% (0.25 g/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Albúmina 20% (0.20 g/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Glutamina 20%
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>g</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Cromo (4 mcg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mcg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Heparina (1000 UI/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>UI</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            L-Carnitina (200 mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Insulina (100 UI/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>UI</span>
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Manganeso (100 mcg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mcg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            MVI
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mL</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Oligoelementos
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mL</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Ácido Folínico (12.5 mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Selenio (40 mcg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mcg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Vitamina C (100 mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Vitamina K (10 mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Zinc (1 mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            L-Cisteina (50mg/mL)
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" /><span>mg</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                OBSERVACIONES
            </x-label>
            <textarea name="" id="" cols="80" rows="5"></textarea>
        </div>
        <div class="flex flex-row gap-4 items-start">
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Fecha de entrega:
                        </x-label>
                        <x-input-solicitud type="date" value="{{ old('') }}" name="" class="w-full"
                            placeholder="" />
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Hora de entrega
                        </x-label>
                        <x-input-solicitud type="date" value="{{ old('') }}" name="" class="w-full"
                            placeholder="" />
                    </div>
                </div>
            </div>
            <div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            NOMBRE DEL MÉDICO*:
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" />
                    </div>
                </div>
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            CÉDULA*:
                        </x-label>
                        <x-input-solicitud value="{{ old('') }}" name="" class="w-full"
                            placeholder="" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end">
            <x-button>
                ENVIAR SOLICITUD
            </x-button>
        </div>


        {{-- <div class="mb-4">
            <x-label class="mb-2">
                Denominación genérica
            </x-label>
            <x-input-solicitud value="{{old('denominacion_generica')}}" name="denominacion_generica" class="w-full" placeholder="Escriba la denominacion genérica del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Denominación comercial
            </x-label>
            <x-input-solicitud value="{{old('nombre_comercial')}}" name="nombre_comercial" class="w-full" placeholder="Escriba la denominacion comercial del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Descripción
            </x-label>
            <x-select class="w-full" name="input_id">
                @foreach ($inputs as $input)
                    <option @selected(old('input_id') == $input->id) value="{{ $input->id }}">{{ $input->description }}</option>
                @endforeach
            </x-select>
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Presentación
            </x-label>
            <x-input-solicitud value="{{old('presentacion_ml')}}" name="presentacion_ml" class="w-full" placeholder="Escriba la presentación del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Osmolaridad
            </x-label>
            <x-input-solicitud  type="number" value="{{old('osmolaridad')}}" name="osmolaridad" class="w-full" placeholder="Escriba la osmolaridad" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Precio por ml
            </x-label>
            <x-input-solicitud type="number" value="{{old('precio_ml')}}" step="0.001" name="precio_ml" class="w-full" placeholder="Escriba el precio por ml del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Categoría
            </x-label>
            <x-select class="w-full" name="category_id">
                @foreach ($categories as $category)
                    <option @selected(old('category_id') == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex justify-end">
            <x-button>
                Crear medicamento
            </x-button>
        </div> --}}
    </form>
</x-admin-layout>
