@csrf
<div class="form-body">
    <x-input name="name" label="Jméno a příjmení" :value="$reader->name" required maxlength="255" autofocus autocomplete="off" />
    <x-input name="email" label="E-mail" type="email" :value="$reader->email" required maxlength="255" autocomplete="off" />
</div>
