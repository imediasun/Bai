
<nav class="bottom_menu main_indent">
    {{ wo_render_breadcrumbs({separator: '/', separatorClass: 'sep'}) }}

    <div class="hold">

{#{% set products = app_tools.links(currentPath) %}#}{#

        #}
{#{% for product in products %}#}{#

            #}
{#<div class="el">#}{#

                #}
{#<div class="big">{{ product.header }}</div>#}{#

                #}
{#<ul>#}{#

                    #}
{#{% for item in product.items %}#}{#

                        #}
{#<li><a href="{{ item.url }}">{{ item.title }}</a></li>#}{#

                    #}
{#{% endfor %}#}{#

                #}
{#</ul>#}{#

            #}
{#</div>#}{#

        #}
{#{% endfor %}#}

        {% set products = app_tools.getFooterLinks() %}
        {% for product in products if products|length > 0 %}
            <div class="el">
                <div class="big">{{ product.groupId.title }}</div>
                <ul>
                    {% for product_ in products if product_.groupId.id == product.groupId.id %}
                        <li><a href="{{ product_.href }}">{{ product_.title }}</a></li>
                    {% endfor %}
                </ul>
            </div>
        {% endfor %}

    </div>
</nav>

