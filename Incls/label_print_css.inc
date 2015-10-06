    <style>
    body {
        width: 8.5in;
        margin: 0in .1875in;
        /* font-size:0.875em;     	 16px x 0.875em = 14px */
        font-size:0.750em;     	/* 16px x 0.750em = 12px */
        }
    .label{
        /* for Avery 5160 labels */
        width:2.025in; 					/* plus .6 inches from padding */
        /* height:0.875in;  plus .125 inches from padding */
        height:0.9125in;				/* optimum height for Chrome printing */
        padding:0.125in .3in 0; 
        margin-right:0.125in; 	/* the gutter */
        float:left;
        text-align:center;
        overflow:hidden;
        outline:0px dotted; 		/* outline doesn't occupy space like border does */
        }
    .page-break  {
        clear: left;
        display:block;
        page-break-after:always;
        }
    </style>