@php $category = \SkillDo\Cms\Support\Cms::getData('category'); @endphp
<div class="col-padding" style="position: relative">
    <h1 class="h1-title">{!! (!empty($category) && !empty($category->name)) ? $category->name : __('Bất động sản') !!}</h1>
</div>
<style>
    .h1-title {
        font-weight: 700;
        font-size: 21px;
        color: #242933;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
</style>

