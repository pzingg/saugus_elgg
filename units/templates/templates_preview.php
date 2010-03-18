<?php

    // Preview template
    
    // Basic page elements
    
        $name = "Basic page elements";
        $heading1 = gettext("Heading one"); // gettext variable
        $heading2 = gettext("Heading two"); // gettext variable
        $bulletList = gettext("A bullet list"); // gettext variable
        $heading3 = gettext("Heading three"); // gettext variable
        $numberedList = gettext("A numbered list"); // gettext variable
        $body = <<< END
        
    <img src="{{url}}_templates/leaves.jpg" width="300" height="225" alt="A test image" align="right" />
    <h1>$heading1</h1>
    <p>Paragraph text</p>
    <h2>$heading2</h2>
    <ul>
        <li>$bulletList</li>
    </ul>
    <h3>$heading3</h3>
    <ol>
        <li>$numberedList</li>
    </ol>
        
END;

        $run_result .= templates_draw(array(
                                                    'context' => 'contentholder',
                                                    'title' => $name,
                                                    'body' => $body
                                                )
                                                );

    // Form elements
    
        $name = "Data input";

        $body = templates_draw(array(
                                                'context' => 'databox',
                                                'name' => gettext("Some text input"),
                                                'column1' => display_input_field(array("blank","","text")),
                                                'column2' => run("display:access_level_select",array("blank","PUBLIC"))
                                            )
                                            );
        $body .= templates_draw(array(
                                                'context' => 'databox1',
                                                'name' => gettext("Some longer text input"),
                                                'column1' => display_input_field(array("blank","","longtext"))
                                            )
                                            );
        $body .= templates_draw(array(
                                                'context' => 'databoxvertical',
                                                'name' => gettext("Further text input"),
                                                'contents' => display_input_field(array("blank","","longtext")) . "<br />" . display_input_field(array("blank","","text")) . "<br /><input type='button' value='Button' />"
                                            )
                                            );
        
        $run_result .= templates_draw(array(
                                                        'context' => 'contentholder',
                                                        'title' => $name,
                                                        'body' => $body,
                                                        'submenu' => ''
                                                    )
                                                    );
?>