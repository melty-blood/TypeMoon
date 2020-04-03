#include <stdio.h>
#include <stdlib.h>
#include <string.h>


// gcc grep_puls.c -o grep_puls

int is_title(char *s)
{
    for (int i = 0; i < strlen(s) - 2; ++i)
    {
        if (s[i] == s[i+1] && s[i] == s[i+2])
            return 1;
    }
    return 0;
}

int main(int argc, char const *argv[])
{
    /* compose command */
    char command[500] = "grep --color=always --exclude-dir={.bzr,CVS,.git,.hg,.svn} ";
    char sep[] = " ";
    for (int i = 1; argv[i] != NULL; ++i)
    {
        strcat(command, sep);
        strcat(command, argv[i]);
    }
    /* find title line */
    char buffer[500];
    char flows[3000] = {0};
    while (fgets(buffer, sizeof(buffer), stdin) != NULL)
    {
        if (is_title(buffer))
        {
            /* use green color to highlight title */
            printf("\033[;32m");
            printf("%s", buffer);
            printf("\033[0m");
            break;
        }
        /* save read string */
        strncat(flows, buffer, (size_t)(3000 - strlen(flows)));
    }
    fflush(stdout);
    /* call grep */
    FILE *fp;
    int c;
    char *flow_point = flows;
    fp = popen(command, "w");
    if (fp != NULL)
    {
        while ((c = *flow_point++) != 0)
            putc(c, fp);
        while ((c = getchar()) != EOF)
            putc(c, fp);
        putc(EOF, fp);
        pclose(fp);
    }
    return 0;
}
